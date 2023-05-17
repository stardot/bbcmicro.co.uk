Running a diff between Mick's collection and bbcmicro.co.uk
===========================================================

1. Visit the following URL to ensure that the archives are up to date:

   https://bbcmicro.co.uk/archdl.php

2. Open a Terminal window.

3. Change directory into the folder containing the scripts, for example:

   cd /Users/MyName/Documents/splitter

4. If this is the first time you are running the script, you will need to give
   it the correct execute permissions by entering the following:

   chmod 755 run-diff.sh

5. Make sure the splitter/ssd-skiplist.txt file is up to date.

6. Run the diff by entering the following:

   ./run-diff.sh

   Once the script has finished, the results can be found in the output folder:

     * The diff results are in diff.txt

     * The archive folder contains the files for archive.org

     * The workspace folder contains all the interim files for reference

Note that the workspace folder is deleted and recreated at the start of each
run, so if you want to keep its contents, make a copy of it first.


Checking download links on bbcmicro.co.uk
=========================================

There is an additional script in the scripts folder: get_bbcmicro_DL_links.pl.

This checks for valid URLs in download buttons on the bbcmicro.co.uk site,
flagging any that don't look correct with "Unmatched link" in the output. The
parameter to the script is the total number of game pages on the site, which you
can find by clicking the >| button on the homepage (at the time of writing this
number is 120).
