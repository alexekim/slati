https://stackedit.io/app#
https://stackedit.io/app#
https://stackedit.io/app#



**

# SLATI (State Legislated Actions on Tobacco Issues)
This repository was created as part of a project to migrate SLATI from http://lungusa2.org/slati/ to Lung.org. The old site is/was hosted on Rackspace, which we want to shutdown. There is a State Tobacco Cessation site hosted on the same server which we are looking to shut down as well.

## 1) Static content
 These pages are simply copies of the originals. HTML content with nothing dynamically loading.
 - PDFs, resources
 - about.html
 - contact.html
 - factsheets_tobaccopolicyreports....html
 - smokefreelaws.html
 - tobacco_control.html
 - tobaccotaxes.html

## 2) Dynamically Loading Content

These pages use AJAX calls from XML files to load content. These XML files are produced via Microsoft Access that Thomas Carr (thomas.carr@lung.org) controls and provides.

 - Appendix B
	 - refer to ajax, promises, sorting
 - Appendix C
 	 - refer to ajax, promises, sorting

 - Appendix D (removed from report)
 	 - refer to ajax, promises, sorting

 - Appendix E
	 - refer to ajax, promises, sorting
 - Appendix F
  	 - refer to ajax, promises, sorting

 - SLATI Overview
  	 - refer to ajax, promises, sorting
 - State Pages
  	 - refer to ajax, promises, sorting



# Tech
1) Ajax, Promises, sorting

 - The appendices, (B, C, E, F) are built with empty tables.
 - The table framework is already hardcoded in HTML.
	 - (B & C) Sometimes it is only the table headers built,
	 - or other times, each table cell is already predetermined.
	 - This largely depends on the type of data available
		 - (E & F) tables with not all states present are typically completely precoded, with limited amounts of data dynamically filled from AJAX calls
 - Otherwise almost all content is dynamically loaded.

**Appendix B**
- starts with hiding all content besides a load spinner. ajax call hides the spinner after data loads and shows the hidden content.
- starts with only table headers. the rest is created by the ajax call
- $.each() with 51 ajax calls, looping through a premade array of state abbreviatons
- within each section of the XML,
	- `$(xml).find('qryXMLSLATIDetail').each(function(i) {}`
		- identifies a series of data to be placed into HTML
**Appendix C**
- starts with hiding all content besides a load spinner. ajax call hides the spinner after data loads and shows the hidden content.
- starts with only table headers. the rest is created by the ajax call
- PROMISE based
	- `try{}catch{}`not supported because of our IE constituents
	- `xmlSortAlpha().then(handleResolveAlpha).catch(handleReject)`
- SORT based.
- Two different ajax calls, one for numeric sorting, one for alphabetic sorting
	- first function
		- pushes State & Value into `var storage`
		- returns a promise to be Resolve or Reject
	- second function `handleResolve()`
		- sorts using `sort()`
		-
- $.each() with 51 ajax calls, looping through a premade array of state abbreviatons
- within each section of the XML,
	- `$(xml).find('qryXMLSLATIDetail').each(function(i) {}`
		- identifies a series of data to be placed into HTML


2) slati.lung.org
cloudflare dns
page rules + cname record
