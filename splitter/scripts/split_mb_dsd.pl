#!/usr/bin/perl
use warnings;
use strict;
use FileHandle;

use lib 'scripts/mmb_utils';
use BeebUtils;

my $TRACK_SIZE=256*10; # 10 sectors per track to split DSDs into SSDs

my $verbose=0;
$verbose=1 if @ARGV && $ARGV[0] eq '-v';
$verbose=2 if @ARGV && $ARGV[0] eq '-vv';

my $rdir="output/workspace/SSDs_from_DSDs";

my %skips; read_skiplist("ssd-skiplist.txt");

foreach my $dsd (<output/workspace/DSD/*.dsd>)
{
  print "Processing $dsd\n";

  my $disk_name=$dsd; $disk_name=~s!^.*/!!; $disk_name=~s!\.dsd$!!;
  $disk_name=clean_disk_name($disk_name);

  # Load the DSD into memory
  my $src_image=BeebUtils::load_external_ssd($dsd,0);
  # Ensure the disk is big enough (MBs disks shouldn't suffer this, but...)
  $src_image .= "\0" x ($TRACK_SIZE*80*2);

  my ($disk0,$disk2);

  foreach my $track (0..79)
  {
    my $offset=$track*$TRACK_SIZE*2; # interleaved
    $disk0 .= substr($src_image,$offset,$TRACK_SIZE);
    $disk2 .= substr($src_image,$offset+$TRACK_SIZE,$TRACK_SIZE);
  }

  # So now $disk0 and $disk2 are SSD images.

  # Let's get a catalogue of each one
  my %cat0=BeebUtils::read_cat(\$disk0);
  my %cat2=BeebUtils::read_cat(\$disk2);

  delete($cat0{""});
  delete($cat2{""});

  # Let's get the !BOOT file from side 0
  my $boot=BeebUtils::ExtractFile(\$disk0,'$.!BOOT',%cat0);

  # Now !BOOT is a BASIC program.  MB put this in a specific
  # format; there's the menu code and then some DATA statements
  # eg
  #   190REM"^A  ***** DRIVE 0 *****
  #   200DATAtitle,PROGRAM
  #   ...
  #   290REM"^A  ***** DRIVE 2 *****
  #   300DATAtitle,PROGRAM
  #   ...
  # end of BASIC
  # (where the ^A character is actually 0x81)
  # Rather than LISTing the !BOOT file, we use some knowledge of BASIC
  # binary format
  #  each line is 0x0D ## ## ## (there those three bytes make up line number
  #    and line length).
  #  We know a REM is 0xF4 and we know a DATA is 0xDC
  #  This makes a "search for data" a little simpler
  my @titles;
  my %games;
  my $rem=chr(244);  my $data=chr(220);  my $end_prog=chr(255); my $red=chr(129);

  my $drive=-1;
  while ($boot)
  {
    last if substr($boot,0,2) eq "\r$end_prog";
    die "Bad !BOOT - Bad Program\n" unless substr($boot,0,1) eq "\r";

    my $len=ord(substr($boot,3,1));
    my $line=substr($boot,4,$len-4);
    $boot=substr($boot,$len,-1);
    
    if ($line=~/^${rem}"${red}(.*DRIVE |DR\.)([02]) */)
    {
      $drive=$2;
      next;
    }

    next if $drive == -1;

    # Some lines may have whitespace at the end
    $line=~s/\s+$//;

    if ($line=~/^${data}(.*),(.*)$/)
    {
      my ($game,$file)=($1,$2);

      # These are not real game entries and just place holders
      if ($game=~/^"?\*/)
      {
        print "  Skipping entry $game,$file\n" if $verbose;
        next;
      }
      $file='$.' . $file unless $file =~ /^.\./;
      $game=clean_game_name($game);
      # Lower case cos FOO and foo same on Beeb.  We force comparisons
      # to lc() versions of filenames in the DATA statements 'cos some
      # games have XYZZY in the DATA statement but may be Xyzzy on disk.
      $games{lc(":$drive.$file")}=$game;
      push @titles,":$drive.$file";
      next;
    }
  }

  # Let's recap; what do we have at this point?
  #  $disk0, $disk2 - SSD images of each side
  #  %cat0, %cat2 - catalogue information for each side
  #  @titles - Start file for each game in :drive.dir.name (:0.$.FOO)
  #  $games - Hash of startfile -> game name.
  # We have enough information now to make each SSD!

  my $saved_count=0; my $skipped_count=0;
  foreach my $title (@titles)
  {
    # Remember lc() for all references to %games
    my $ssdname="$rdir/${disk_name}-$games{lc($title)}.ssd";
    my ($side,$start,$dtitle)=($title=~/^:(.)\.(.\.(.*))$/);

    my $ssd=BeebUtils::blank_ssd();

    # *opt4,3
    BeebUtils::opt4(\$ssd,3);
    BeebUtils::set_ssd_title(\$ssd,$dtitle);

    my $boot="*BASIC\rPAGE=&1900\r*FX21\rCLOSE#0:CHAIN \"$dtitle\"\r";
    BeebUtils::add_content_to_ssd(\$ssd,'$.!BOOT',$boot,0,0,1);

    my ($this_disk,$this_cat);
    if ($side == 0)
    {
      $this_disk=\$disk0; $this_cat=\%cat0;
    }
    else
    {
      $this_disk=\$disk2; $this_cat=\%cat2;
    }

    my $found=0;
    my $added=0;
    foreach (sort { $this_cat->{$a}{start} <=> $this_cat->{$b}{start} } keys %$this_cat)
    {
      my $this_name=lc($this_cat->{$_}{name});
      $found=1 if $this_name eq lc($start);
      next unless $found;
      last if defined($games{":$side.$this_name"}) and $this_name ne lc($start);

      $added++;
      if ($added >= 31)
      {
        print "  WARNING: $ssdname catalogue is full.  ";
        delete_boot(\$ssd);
      }
      my $data=BeebUtils::ExtractFile($this_disk,$this_cat->{$_}{name},%$this_cat);
      eval
      {
        add_to_disk(\$ssd,$this_cat,$_,$data);
      };
      # If that failed to add then maybe the disk was full; delete !BOOT
      # and retry
      if ($@)
      {
        print "  WARNING: $ssdname disk is full.  ";
        delete_boot(\$ssd);
        add_to_disk(\$ssd,$this_cat,$_,$data);
      }

      print "  On $dsd :$side.$this_cat->{$_}{name} is unlocked\n" 
        if ($verbose == 2 && !$this_cat->{$_}{locked});
    }
    if ($found)
    {
      if (exists $skips{$ssdname})
      {
        print "  Skipping $ssdname\n" if $verbose == 2;
        $skipped_count++;
      }
      else
      {
        BeebUtils::write_ssd(\$ssd,$ssdname);
        print "  Saved $ssdname\n" if $verbose == 2;
        $saved_count++;
      }
    }
    else
    {
      print "  Could not find :$side.$start - $ssdname skipped\n";
    }
  }
  print "  Saved $saved_count games\n" if $verbose;
  print "  Skipped $skipped_count games\n" if $verbose == 2;
}

sub read_skiplist # games in skiplist WON'T be output as .SSDs
{
  my ($skipfile)=@_;
  if (-f $skipfile)
  {
    my $fh=new FileHandle "<$skipfile";
    die "Could not open $skipfile: $!\n" unless $fh;
  
    my $num_skips=0;  
    while (<$fh>)
    {
      chomp;
      if ($_ ne "")
      {
        $skips{"$rdir/$_"}++;
        $num_skips++;
      }
    }
    close($fh) or die $!;
    print "Read skipfile: $num_skips entries\n" if $verbose == 2;
  }
}

sub clean_game_name
{
  my ($game)=@_;
  # Remove Teletext characters from filename
  $game=~s/[^A-Za-z0-9\s\:]//g;

  # Reword filenames beginning "Adventure n: "
  $game=~s/^(Adventure\s[0-9]+)\:\s(.*)/$2-$1/;

  # Remove definite or indefinite article from start of filename
  $game=~s/^\s*The\s//;
  $game=~s/^\s*A\s//;
  $game=~s/^\s*An\s//;

  $game=~s/[^A-Za-z0-9\-]//g;  # Make the SSD filename "safe"

  return $game;
}

sub clean_disk_name
{
  my ($diskname)=@_;
  
  # e.g. AltD001 -> DiscA01
  $diskname=~s/^AltD0([0-9]{2})/DiscA$1/;
  
  # e.g. AltD173 -> DiscA173
  $diskname=~s/^AltD([0-9]{3})/DiscA$1/;
  
  $diskname=~s/^Orig0([0-9]{2})/DiscO$1/;
  $diskname=~s/^Orig([0-9]{3})/DiscO$1/;
  
  # e.g. Disc105SE -> Disc105
  $diskname=~s/^(Disc[0-9]{3})SE/$1/;  
    
  return $diskname;
}

sub delete_boot
{
  my ($ssd)=@_;  # Reference to the image
  BeebUtils::delete_file(1,'$.!BOOT',$ssd);
  BeebUtils::opt4($ssd,0);
  BeebUtils::compact_ssd($ssd);
}

sub add_to_disk
{
  my ($ssd,$cat,$entry,$data)=@_;  # $ssd is a reference

  BeebUtils::add_content_to_ssd($ssd,$cat->{$entry}{name},
                                     $data,
                                     $cat->{$entry}{load},
                                     $cat->{$entry}{exec},
                                     $cat->{$entry}{locked});
}