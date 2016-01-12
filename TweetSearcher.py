from tweepy import Stream
from tweepy import OAuthHandler
from tweepy.streaming import StreamListener
import json
import re
import csv

# ADD YOUR OWN KEYS HERE
consumer_key = ''
consumer_secret = ''
access_token = ''
access_secret = ''


file_name = "tweets.csv"
csvfile = file(file_name, "w")
csvwriter = csv.writer(csvfile)
row = [ "Title", "Content", "Lat", "Long" ]

#just had this stuff here so I could print out test tweets. Lines 21-25 can be ignored
tweets = []
screen_names = []
content = []
lon = []
lat = []
count = 0
max_tweets = 25 # How many tweets you want here


# CALIFORNIA BASED TWEETS WITH THIS BOUNDING BOX
California_North = 42.0095169
California_South = 32.5342626
California_West = -124.415165
California_East = -114.13139260000003


class StdOutListener(StreamListener):
    def on_data(self, data):
        global count

        if(count < max_tweets):
            json_data = json.loads(data) # load up tweet's JSON data
            tweets.append(json_data)
            if((json_data.get("geo") is not None) and (json_data.get("text") is not None)): # check if the tweet has geo enabled and text
                longitude = json_data["geo"]["coordinates"][0] # store latitude and longitude
                latitude = json_data["geo"]["coordinates"][1]
                if((longitude > California_South) and (longitude < California_North) and (latitude < California_East) and (latitude > California_West)): # check if tweet is in California
                    print "---------- " + str(count + 1) + " ----------" # print some relevant information
                    print str(longitude) + ", " + str(latitude)
                    print json_data["text"]
                    Title = json_data["user"]["screen_name"]
		    # regex for hashtags rather than just words in general -> could use, but don't have to
                    # Content = re.findall(r"#(\w+)", tweets[count]["text"].encode('ascii', 'ignore'))
                    Content = tweets[count]["text"].encode('ascii', 'ignore')
                    symbols = "~`!@#$%^&*()_+{}[]|\:'<>,;./?" # remove symbols just to make it easier to read
                    Content = Content.replace("\n", " ")
                    for i in range(0, len(symbols)):
                            Content = Content.replace(symbols[i],"")
                    Lat = json_data["geo"]["coordinates"][0] # IDK why I did this a second time
                    Long = json_data["geo"]["coordinates"][1]
                    row = [ Title, Content, Lat, Long ]
                    csvwriter.writerow(row) # write to CSV file
                    count += 1
                    return True
        else:
            csvfile.close()
            return False

    def on_error(self, status):
        print status

if __name__ == '__main__':
        l = StdOutListener()
        auth = OAuthHandler(consumer_key, consumer_secret)
        auth.set_access_token(access_token, access_secret)
        stream = Stream(auth, l)
        word = raw_input("Enter a word/hashtag you want to search for: ")
        stream.filter(track=[str(word)])
