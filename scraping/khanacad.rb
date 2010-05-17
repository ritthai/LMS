#!/usr/bin/ruby

require 'net/http'

http = Net::HTTP.new('www.khanacademy.org', 80)
resp, data = http.get("/", nil)
print "<xml>"
data.scan(/<br><A href="(.{40,60})">(.{2,50})<\/A>/m) { |a|
	print "<subject title=\"#{a[0]}\" link=\"#{a[1]}\" />\n"
}
print "</xml>"
