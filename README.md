# TweetMapper
ENGR 180 Final Project working with Python, Twitter API, and Google Maps API

- Introducing the poor man's version of mapping tweets based on a keyword/hashtag for the state of California.
- Done at UC Merced Fall 2015 semester

STEPS TO RUN MY PROJECT

1. Run TweetSearcher.py with desired number of tweets (can change in file)

2. Wait for results to be completed

3. Open output file in Excel then try to close it

	3a. Should prompt Yes/No/Cancel options
	
	3b. Press Yes and overwrite the same file

	3c. Try to close again and press yes (should instantly close Excel)

4. Start XAMPP -> Apache server on

5. Move TweetPlotter.php and output file into C:\xampp\htdocs

	5a. If installed elsewhere, just find the htdocs folder

6. Open Google Chrome or FireFox
	
	6a. Type localhost/TweetPlotter.php into the url

	6b. If a different file name, just replace TweetPlotter.php with it

7. Should see a map with the plotted tweets

	7a. If it is just a blank page, go back to step 3 and try to fix it
