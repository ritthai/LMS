#include <stdio.h>

int main(int argc, char **argv) {
	FILE *fpin = fopen("courses.xml", "r");
	FILE *fpout = fopen("courses2.xml", "w");
	unsigned char c;
	
	while((c=fgetc(fpin)) && !feof(fpin))
		if(c < 128 && c > 0) fputc(c, fpout);
	
	fclose(fpin);
	fclose(fpout);
}

