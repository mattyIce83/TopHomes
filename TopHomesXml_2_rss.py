from ftplib import FTP
from datetime import datetime
from shutil import copyfile
import sys, os
path = 'C:/Program Files/Python36/images/'
rightNow = (str(datetime.now()))
linesUntilContent = 5
splitImages = []
matchImage = ''
imageCheck = False

#cross reference images with artice ids
def imageChecker(ADID):
	global imageCheck
	global matchImage
	for image in images:
		if ADID in image and 'Image' in image:
			imageCheck = True
			splitImages = image.split()
			matchImage = splitImages[8]

#Start writing to our new file
newFeed= open("newfeed.txt","w+")
newFeed.write('<rss version="2.0"> \n')
newFeed.write('	<channel> \n')
newFeed.write('		<link>http://www.tampabay.com/marketplace</link> \n')
newFeed.write('		<description>Top homes from Tampabay.com</description> \n')
newFeed.write('		<copyright>Copyright 2018 St. Petersburg Times</copyright> \n')
newFeed.write('		<pubDate>'+str(datetime.now())+'</pubDate> \n')
newFeed.write('		<language>en-us</language> \n')
newFeed.write('			<item> \n')

#connect to the ftp
ftp = FTP('ftp.tampabay.com')
ftp.login('jhaynes','kojak123')
#put the image files in  a list
ftp.cwd('/tophomes/images/')
images = []
ftp.dir(images.append)
ftp.cwd('../')
files = []
ftp.dir(files.append)
for image in images:
	splitImage = image.split()
	fullPath = path+splitImage[8]
	shortPath= splitImage[8]
	try:
		imgfile = open(fullPath, 'wb')
		ftp.retrbinary('RETR %s' % shortPath, imgfile.write)
		imgfile.close()
	except:
		imgfile.close()
		os.remove(fullPath)

#get only the file we want from the /tophomes directory
filesNum = len(files)
topFeed = files[filesNum-1]
topFile = topFeed.split()
topFileName = "ftp://ftp.tampabay.com/tophomes/"+topFile[8]

#copy the file from the FTP so that we can use it
file = open('test.txt', 'wb')
ftp.retrbinary('RETR %s' % topFile[8], file.write)
file.close()

#read the file
file = open('test.txt','r')

for counter, line in enumerate(file):
	if counter >= linesUntilContent:
		if "<ad-number>"  in line: 
			imageChecker(line[19:25])
			newFeed.write("			"+line)
		if "</pub-code>" in line:
			newFeed.write("			"+line)
			newFeed.write('			</item> \n')
			newFeed.write('			<item> \n')
		if "<image>" in line and imageCheck == True:
			newFeed.write("				<image>"+matchImage+"</image>\n")
			imageCheck = False
		elif "<image>" in line and imageCheck == False:
			newFeed.write("				<image></image>\n")
		else:
			newFeed.write("			"+line)

newFeed.write('	</channel> \n')
newFeed.write('</rss> \n')
	#	newFeed.write(str(counter)+ line+"\n")



