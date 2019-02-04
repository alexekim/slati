
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
	- first function `xmlSortAlpha() or xmlSortNumeric()`
		- pushes State & Value into `var storage`
		- returns a promise to be Resolve or Reject
	- second function `handleResolveAlpha() or handleResolveNumeric()`
		- sorts `storage` variable using `sort()`
		- then loops through `storage` while `.append()`puts the data into the table
- $.each() with 51 ajax calls, looping through a premade array of state abbreviatons
- within each section of the XML,
	- `$(xml).find('qryXMLSLATIDetail').each(function(i) {}`
		- identifies a series of data to be placed into HTML
		- adds each data point to `storage`

NOTE: `storage` is a global variable. each time a `sort()` is requested by the user, it is cleared out by the function first, then the process starts all over again.

**Appendix E**

 - simple AJAX call. no promises, no sorting
 - Not all states have data. the table is premade with selected states that do have data
	 - the field "*Issue Area Where Preemption Exists*" is hard coded. this is because of complicated XML organization, or lack of it
 - The field "*Specific Provisions Preempted*" is dynamically filled

**Appendix F**
 - simple `$.each()` loop of all `$.ajax()` for each state. If state has no data, it is skipped.
 - No promises, no sorting
 - Not all states have data. the table is premade with selected states that do have data
	 - the field "*States*" is hard coded
 - The fields  "*Year*" and "*Citation*" are dynamically filled
 - Citation click/togggle hide/show feature is inline JavaScript
	 - `onclick=""` event is jQuery `toggleClass()` method that gives a hide or show css class
	 - citation detail itself is part of the XML call.

# DNS/URLs
**Cloudflare DNS changes**

*PAGE RULE:*
 1. within lung.org dashboard, use **Page Rules**
 2. **Create Page Rule**
 3. if URL matches: **slati.lung.org**
 4. setting: **Forwarding URL**, status code: **302**, destination URL: **https://www.lung.org/our-initiatives/tobacco/reports-resources/slati/**
 5. save this page rule

*DNS Record:*

 1. within lung.org dashboard, use **DNS**
 2. create new record
 3. CNAME
 4. name: **slati** (slati.lung.org)
 5. IPv4: **@** (lung.org)
 6. TLL: **automatic**
 7. **Add Record**
This was changed from an **A-record** that went to the rackspace server's IP address
