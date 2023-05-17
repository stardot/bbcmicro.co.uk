# Create folder structure
echo "> Creating output folders"
rm -fr output
mkdir -p output/workspace/mick_zips
mkdir -p output/workspace/DSD
mkdir -p output/workspace/SSDs_from_DSDs
mkdir -p output/workspace/site_ssds
mkdir -p output/workspace/bbcm
mkdir -p output/archive

# Fetch BBC Micro games archive
echo "> Fetching bbcmicro.co.uk SSDs (this may take a while)"
curl -sS https://bbcmicro.co.uk/tmp/BBCMicroDiscs.zip -o output/workspace/BBCMicroDiscs.zip

# Unzip the game discs from bbcmicro.co.uk and put them in site_ssds
echo "> Unzipping bbcmicro.co.uk SSDs"
unzip -qq -d output/workspace/site_ssds output/workspace/BBCMicroDiscs.zip

# Copy the relevant site SSDs to bbcm
echo "> Extracting relevant bbcmicro.co.uk SSDs"
find output/workspace/site_ssds -name "Disc*.ssd" | grep -vFf ssd-skiplist.txt | xargs -I{} cp {} output/workspace/bbcm

# Fetch Mick's zip files from Stardot and put them in mick_zips
echo "> Fetching Mick's collection from Stardot (this may take a while)"
perl scripts/get_Micks_DSD_DL_links.pl > output/workspace/get_Micks_DSD_DL_links.txt

# Unzip them into DSD (using find to prevent summary message)
echo "> Unzipping Mick's collection"
find output/workspace/mick_zips -name "*.zip" -print0 | xargs -0 -n1 unzip -qq -d output/workspace/DSD

# Split Mick's DSDs into SSDs and put them in SSDs_from_DSDs
echo "> Splitting Mick's collection into SSDs"
ls -C output/workspace/DSD | expand > output/workspace/split.txt
echo >> output/workspace/split.txt
perl scripts/split_mb_dsd.pl -vv >> output/workspace/split.txt

# Diff the files
echo "> Running diff between Mick's collection and bbcmicro.co.uk SSDs"
cd output/workspace
diff -q -x 'Disc999*' SSDs_from_DSDs bbcm | sort > ../diff.txt
echo >> ../diff.txt
diff -q SSDs_from_DSDs bbcm | grep 'Disc999*' | sort >> ../diff.txt
cd ../..

# Create archive folder
echo "> Creating archive folder"
cd output/workspace
zip -qr ../../output/archive/DSD.zip DSD
zip -qr ../../output/archive/SSDs_from_DSDs.zip SSDs_from_DSDs
zip -qr ../../output/archive/bbcm.zip bbcm
cd ../../
cp output/diff.txt output/archive
cp output/workspace/split.txt output/archive
cp ssd-skiplist.txt output/archive
cp output/workspace/get_Micks_DSD_DL_links.txt output/archive

echo "> All done, see output folder for results"
