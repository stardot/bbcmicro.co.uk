#!/usr/bin/perl -w
use strict;
use warnings;
use feature qw(say);
use FileHandle;

# use 5.010;
 
use HTTP::Tiny;
# use Data::Dumper qw(Dumper);

my $url1 = 'https://stardot.org.uk/forums/viewtopic.php?f=32&t=8270';
my $response1 = HTTP::Tiny->new->get($url1);
my $numPages;

if ($response1->{success}) 
{
	my $html1 = $response1->{content};
	open my $fh1, '<', \$html1 or die $!;
	
	while (<$fh1>)
	{
		if ($_ =~ /<li class="ellipsis" role="separator"><span>/)
		{
			my $next_line1 = <$fh1>;
			$next_line1 =~ /^.+start=(\d+)" role="button">.+$/;
			$numPages = $1;
			say "Last page in main game list is start=$numPages";
			last;
		}
	}
	close $fh1 or die $!;

} else {
	die "ERROR getting first page for thread 8270\n";
}

my $url2 = 'https://stardot.org.uk/forums/viewtopic.php?f=32&t=9049';
my $response2 = HTTP::Tiny->new->get($url2);
my $numPages2;

if ($response2->{success}) 
{
	my $html2 = $response2->{content};
	open my $fh2, '<', \$html2 or die $!;
	
	while (<$fh2>)
	{
		if ($_ =~ /start=(\d+)"/)
		{
			$numPages2 = $1;
		}

		if ($_ =~ /<li class="arrow next"/)
		{
			say "Last page in alternative game list is start=$numPages2";
			last;
		}
	}
	close $fh2 or die $!;

} else {
	die "ERROR getting first page for thread 9049\n";
}

#my $numPages=$ARGV[0];
#die "First [end offset] parameter is missing\n"
#	if (!defined($numPages) || $numPages eq ""); 

my $numLinks=0;

for (my $i=0; $i<=$numPages; $i+=30) 
{
	my $url = 'https://stardot.org.uk/forums/viewtopic.php?f=32&t=8270&start='.$i;
	
	my $response = HTTP::Tiny->new->get($url);

	if ($response->{success}) 
	{
		my $html = $response->{content};
	  # print Dumper $response->{success};
	  # print Dumper $html;

		open my $fh, '<', \$html or die $!;
		
		while (<$fh>)
		{
			if ($_ =~ /\<a class\=\"postlink\" href\=\"\.\/(download\/file\.php\?id\=[0-9]+).*\>(Disc[0-9]+(SE)*\.zip)\</)
			{
				$numLinks++;
				say '<a href="https://stardot.org.uk/forums/'.$1.'">'.$2.'</a>';
				
				my $dl_link = "https://stardot.org.uk/forums/".$1;
				my $filename = $2;

				my $response2 = HTTP::Tiny->new->get($dl_link);
				if ($response2->{success}) 
				{
					my $target = "output/workspace/mick_zips/$filename";
					die "$target already exists!\n" if -f $target;
					my $fh2=new FileHandle ">$target";
					die "Could not open $target: $!\n" unless $fh2;
					binmode($fh2);
					
					print $fh2 $response2->{content};
					
					close($fh2) or die $!;
				}
				else
				{
					die "ERROR: Couldn't download $filename\n";
				}
			}
			else
			{
				# say "Link not found on this line";
			}
		}
		close $fh or die $!;
	}
	else
	{
		die "ERROR getting page with offset $i\n";
	}
}

#my $numPages2=$ARGV[1];
#die "Second [end offset] parameter is missing\n" 
#	if (!defined($numPages2) || $numPages2 eq ""); 
	
for (my $i=0; $i<=$numPages2; $i+=30) 
{
	my $url = 'https://stardot.org.uk/forums/viewtopic.php?f=32&t=9049&start='.$i;
	
	my $response = HTTP::Tiny->new->get($url);

	if ($response->{success}) 
	{
		my $html = $response->{content};
	  # print Dumper $response->{success};
	  # print Dumper $html;

		open my $fh, '<', \$html or die $!;
		
		while (<$fh>)
		{
			if ($_ =~ /\<a class\=\"postlink\" href\=\"\.\/(download\/file\.php\?id\=[0-9]+).*\>((AltD|Orig)[0-9]+\.zip)\</)
			{
				$numLinks++;
				say '<a href="https://stardot.org.uk/forums/'.$1.'">'.$2.'</a>';
				
				my $dl_link = "https://stardot.org.uk/forums/".$1;
				my $filename = $2;

				my $response2 = HTTP::Tiny->new->get($dl_link);
				if ($response2->{success}) 
				{
					my $target = "output/workspace/mick_zips/$filename";
					die "$target already exists!\n" if -f $target;
					my $fh2=new FileHandle ">$target";
					die "Could not open $target: $!\n" unless $fh2;
					binmode($fh2);
					
					print $fh2 $response2->{content};
					
					close($fh2) or die $!;
				}
				else
				{
					die "ERROR: Couldn't download $filename\n";
				}
			}
			else
			{
				# say "Link not found on this line";
			}
		}
		close $fh or die $!;
	}
	else
	{
		die "ERROR getting page with offset $i\n";
	}
}

say "Found $numLinks download links";