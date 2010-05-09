#!/usr/bin/perl

open(TEXTDUMP,"<courses.xml");
open(SANITIZED,">courses2.xml");

while( <TEXTDUMP> ) {

chomp($_);
$line = $_	;

$line =~ s/\x89//g;
$line =~ s/&//g;
print SANITIZED "$line\n";
}

close(SANITIZED);
close(TEXTDUMP);
