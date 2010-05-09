#!/usr/bin/ruby

require 'net/http'
http = Net::HTTP.new('ugradcalendar.uwaterloo.ca', 80)
resp, data = http.get("/default.aspx?PageID=616", nil)
b = ""
print "<body>"
data.scan(/;Code=(...?.?.?.?.?)&amp;/m) { |a|
	$stdout.flush
	if !a[0].eql?(b)
		print "<coursecode>"
		print a[0]
		print "</coursecode>\n"
	end
	b = a[0]
	http2 = Net::HTTP.new('www.ucalendar.uwaterloo.ca', 80)
	resp2, data2 = http2.get("/1011/COURSE/course-"+b+".html", nil)
	print "<course>"
	$stdout.flush
	data2.scan(/<center><table border=0 width=80%><tr><td align=left><B><a name = "(.{4,8})"><\/a>.{0,30}<\/b><\/td><td align=right>Course ID: .{4,8}<\/td><\/tr><tr><td colspan=2><b>(.{5,50})<\/B><\/td><\/tr><tr><td colspan=2>([^\[<]{10,500})<\/td>/m) { |c|
		$stdout.flush
		print "<code>"+c[0]+"</code>\n"
		print "<title>"+c[1]+"</title>\n"
		print "<description>"+c[2]+"</description>\n"
	}
	print "</course>\n\n"
}
print "</body>"
