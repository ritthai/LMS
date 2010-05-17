#!/usr/bin/ruby

require 'net/http'
require 'cgi'

http = Net::HTTP.new('www.khanacademy.org', 80)
resp, data = http.get("/", nil)
print "<xml>"
data.scan(/<br><A href="(.{40,60})">(.{2,50})<\/A>/m) { |a|
	print "<subject title=\"#{CGI.escapeHTML(a[1])}\" link=\"#{CGI.escapeHTML(a[0])}\" />\n"
}
print "</xml>"
