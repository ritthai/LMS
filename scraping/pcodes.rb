#!/usr/bin/ruby

require 'net/http'
http = Net::HTTP.new('ugradcalendar.uwaterloo.ca', 80)
resp, data = http.get("/default.aspx?PageID=616", nil)
data.match(/;Code=(...?.?.?.?.?)&amp;/m)
print $1
