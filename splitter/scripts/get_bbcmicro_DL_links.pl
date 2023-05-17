#!/usr/bin/perl -w
use strict;
use warnings;
use feature qw(say);

# use 5.010;
 
use HTTP::Tiny;
# use Data::Dumper qw(Dumper);
 
my $numPages=$ARGV[0];
die "[number of pages] parameter is missing\n" 
	if (!defined($numPages) || $numPages eq ""); 
 
my $numButts=0;
my $numLinks=0;

for (my $i=1; $i<=$numPages; $i++) 
{
	my $url = 'http://bbcmicro.co.uk/?page='.$i;
	
	my $response = HTTP::Tiny->new->get($url);

	if ($response->{success}) 
	{
		my $html = $response->{content};
	  # print Dumper $response->{success};
	  # print Dumper $html;

		open my $fh, '<', \$html or die $!;
		
		while (<$fh>)
		{
			if ($_ =~ /\>Download\</)
			{
				$numButts++;
				
				if ($_ =~ /\<a href\=\"(.*gameimg\/discs\/[0-9]+\/(.*\.(dsd|ssd|zip|adl)))\".*\>Download\</i)
				{
					$numLinks++;
					say '<a href="http://bbcmicro.co.uk/'.$1.'">'.$2.'</a>';
				}
				else
				{
					chomp;
					say 'Unmatched link: ' . $_;
				}
			}
		}
		close $fh or die $!;
	}
	else
	{
		die "ERROR getting page $i\n";
	}
}

say "Found $numButts download buttons";
say "Found $numLinks download links";