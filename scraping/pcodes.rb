#!/usr/bin/ruby

require 'net/http'
require 'rubygems'
require 'htmlentities'
require 'cgi'
require 'active_support'

def nice_slug(str)
	accents = { 
		['á','à','â','ä','ã'] => 'a',
		['Ã','Ä','Â','À','�?'] => 'A',
		['é','è','ê','ë'] => 'e',
		['Ë','É','È','Ê'] => 'E',
		['í','ì','î','ï'] => 'i',
		['�?','Î','Ì','�?'] => 'I',
		['ó','ò','ô','ö','õ'] => 'o',
		['Õ','Ö','Ô','Ò','Ó'] => 'O',
		['ú','ù','û','ü'] => 'u',
		['Ú','Û','Ù','Ü'] => 'U',
		['ç'] => 'c', ['Ç'] => 'C',
		['ñ'] => 'n', ['Ñ'] => 'N'
	}
	accents.each do |ac,rep|
		ac.each do |s|
			str = str.gsub(s, rep)
		end
	end
	str
end

http = Net::HTTP.new('ugradcalendar.uwaterloo.ca', 80)
resp, data = http.get("/default.aspx?PageID=616", nil)
b = ""
print "<body>"
coder = HTMLEntities.new
data.scan(/;Code=(...?.?.?.?.?)&amp;/m) { |a|
	$stdout.flush
	if !a[0].eql?(b)
		print "<coursecode>"
		print a[0]
		print "</coursecode>\n"
		b = a[0]
		http2 = Net::HTTP.new('www.ucalendar.uwaterloo.ca', 80)
		resp2, data2 = http2.get("/1011/COURSE/course-"+b+".html", nil)
		print "<course>"
		$stdout.flush
		data2.scan(/<center><table border=0 width=80%><tr><td align=left><B><a name = "(.{4,8})"><\/a>.{0,30}<\/b><\/td><td align=right>Course ID: .{4,8}<\/td><\/tr><tr><td colspan=2><b>(.{5,50})<\/B><\/td><\/tr><tr><td colspan=2>([^\[<]{10,500})<\/td>/m) { |c|
			$stdout.flush
				u1 = nice_slug(c[0]);
#u1 = coder.encode(u1, :named)
			u1 = CGI.escapeHTML(u1)
				u2 = nice_slug(c[1]);
#u2 = coder.encode(u2, :named)
			u2 = CGI.escapeHTML(u2)
				u3 = nice_slug(c[2]);
#u3 = coder.encode(u3, :named)
			u3 = CGI.escapeHTML(u3)
				print "<code>"+u1+"</code>\n"
				print "<title>"+u2+"</title>\n"
				print "<description>"+u3+"</description>\n"
		}
	print "</course>\n\n"
	end
}
print "</body>"
