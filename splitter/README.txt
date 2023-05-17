Running a diff between Mick's collection and bbcmicro.co.uk
===========================================================

1. Visit https://bbcmicro.co.uk/archdl.php to ensure the zips are up to date.

2. Make sure the ssd-skiplist.txt file is up to date.

3. Open a Terminal window.

4. Change directory into the folder containing the scripts, for example:

   cd /Users/MyName/Documents/splitter

5. Download Mick's latest discs and run the diff by entering:

   ./run-diff.sh

   The results can be found in the output folder:

     * The diff results are in diff.txt.

     * The archive folder contains the files to upload to archive.org.

     * The workspace folder contains all the interim files for reference.

The workspace folder is cleared out at the start of each run, so make a copy
of the contents first if required.

Setting script permissions
==========================

Before you can run this script, you will need to give the run-diff.sh script
the correct execute permissions. To do this:

1. Change directory into the folder containing the scripts, for example:

   cd /Users/MyName/Documents/splitter

2. Set permissions as follows:

   chmod 755 run-diff.sh

