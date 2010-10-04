#!/usr/bin/ruby

require 'net/http'
require 'cgi'

http = Net::HTTP.new('ugradcalendar.uwaterloo.ca', 80)
resp, data = http.get("/page/Course-Descriptions-Index", nil)
#data.scan(/<tr>\s*<td>(.{3,80})<\/td>\s*<td>(.{2,10})<\/td>(\s*<td> <\/td>\s*)*\s*<td><a shape="rect" href="\/courses.aspx?Code=([A-Z]{2-6})/m) { |a|
data.scan(/<tr>\s*<td>([^<]{3,80})<\/td>.{10,280}Code=([A-Z]{2,8})&/m) { |a|
	a[0].strip!()
	a[0].gsub!("\'", "\\\\'")
	a[0].gsub!(/\W*$/, '')
	print "'#{a[1]}' => '#{a[0]}',\n"
}
