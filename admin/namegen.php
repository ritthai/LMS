<?php
$letter[0] = "a";
$letter[1] = "ba";
$letter[2] = "be";
$letter[3] = "bi";
$letter[4] = "bo";
$letter[5] = "bu";
$letter[6] = "by";
$letter[7] = "ca";
$letter[8] = "ce";
$letter[9] = "ci";
$letter[10] = "co";
$letter[11] = "cu";
$letter[12] = "cy";
$letter[13] = "da";
$letter[14] = "de";
$letter[15] = "di";
$letter[16] = "do";
$letter[17] = "du";
$letter[18] = "dy";
$letter[19] = "e";
$letter[20] = "fa";
$letter[21] = "fe";
$letter[22] = "fi";
$letter[23] = "fo";
$letter[24] = "fu";
$letter[25] = "fy";
$letter[26] = "ga";
$letter[27] = "ge";
$letter[28] = "gi";
$letter[29] = "go";
$letter[30] = "gu";
$letter[31] = "gy";
$letter[32] = "ha";
$letter[33] = "he";
$letter[34] = "hi";
$letter[35] = "ho";
$letter[36] = "hu";
$letter[37] = "hy";
$letter[38] = "i";
$letter[39] = "ja";
$letter[40] = "je";
$letter[41] = "ji";
$letter[42] = "jo";
$letter[43] = "ju";
$letter[44] = "jy";
$letter[45] = "ka";
$letter[46] = "ke";
$letter[47] = "ki";
$letter[48] = "ko";
$letter[49] = "ku";
$letter[50] = "ky";
$letter[51] = "la";
$letter[52] = "le";
$letter[53] = "li";
$letter[54] = "lo";
$letter[55] = "lu";
$letter[56] = "ly";
$letter[57] = "ma";
$letter[58] = "me";
$letter[59] = "mi";
$letter[60] = "mo";
$letter[61] = "mu";
$letter[62] = "my";
$letter[63] = "na";
$letter[64] = "ne";
$letter[65] = "ni";
$letter[66] = "no";
$letter[67] = "nu";
$letter[68] = "ny";
$letter[69] = "o";
$letter[70] = "pa";
$letter[71] = "pe";
$letter[72] = "pi";
$letter[73] = "po";
$letter[74] = "pu";
$letter[75] = "py";
$letter[76] = "qu";
$letter[77] = "ra";
$letter[78] = "re";
$letter[79] = "ri";
$letter[80] = "ro";
$letter[81] = "ru";
$letter[82] = "ry";
$letter[83] = "sa";
$letter[84] = "se";
$letter[85] = "si";
$letter[86] = "so";
$letter[87] = "su";
$letter[88] = "sy";
$letter[89] = "ta";
$letter[90] = "te";
$letter[91] = "ti";
$letter[92] = "to";
$letter[93] = "tu";
$letter[94] = "ty";
$letter[95] = "u";
$letter[96] = "va";
$letter[97] = "ve";
$letter[98] = "vi";
$letter[99] = "vo";
$letter[100] = "vu";
$letter[101] = "vy";
$letter[102] = "wa";
$letter[103] = "we";
$letter[104] = "wi";
$letter[105] = "wo";
$letter[106] = "wu";
$letter[107] = "wy";
$letter[108] = "x";
$letter[109] = "ya";
$letter[110] = "ye";
$letter[111] = "yi";
$letter[112] = "yo";
$letter[113] = "yu";
$letter[114] = "y";
$letter[115] = "za";
$letter[116] = "ze";
$letter[117] = "zi";
$letter[118] = "zo";
$letter[119] = "zu";
$letter[120] = "zy";
$letter[121] = "shi";
$letter[122] = "n";

function getSyllable() {
	global $letter;
	return $letter[rand(0,122)];
}
function getName($min_len=2, $max_len=4) {
	$len = rand($min_len, $max_len);
	$ret = "";
	for($i=0; $i<$len; $i++)
		$ret .= getSyllable();
	return $ret;
}
?>
<html><head></head>
<body>
<?php
for($i=0; $i<25; $i++)
	echo "<p>".getName()."</p>";
?>
</body>
</html>

