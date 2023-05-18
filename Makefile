.PHONY: clean

PROGNAME=bounceman.phar
SRCS=$(wildcard src/*)

${PROGNAME}: ${SRCS}
	php -dphar.readonly=0 `which phar` pack -f ${PROGNAME} -l 1 -s src/phar-stub.php -c gz src

clean:
	rm -f ${PROGNAME}
