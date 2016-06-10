VeryPDF PDF Font Extractor Command Line v2.0
Extract embedded fonts from PDF files and save to .ttf and .cff files
Copyright 1996-2012 VeryPDF.com Inc.
Web: http://www.verypdf.com
Email: support@verypdf.com
Build date: May 30 2012
Usage: pdffont [options] <PDF-file>
  -f <int>     : first page to examine
  -l <int>     : last page to examine
  -opw <string>: owner password (for encrypted files)
  -upw <string>: user password (for encrypted files)
  -img         : convert TTF fonts to image files
  -h           : print usage information
  -$ <string>  : input your license key
Example:
   pdffont.exe C:\in.pdf C:\out
   pdffont.exe -f 1 -l 1 C:\in.pdf C:\out
   pdffont.exe -opw 123 -upw 456 C:\in.pdf C:\out
