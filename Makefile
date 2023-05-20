.PHONY: clean

PROGNAME=bounceman.phar
SRCS=$(wildcard src/*)

${PROGNAME}: ${SRCS}
	@echo "Building phar file.."
	php -dphar.readonly=0 `which phar` pack -f ${PROGNAME} -l 1 -s src/phar-stub.php -c gz src
	@echo "Done!"

clean:
	@echo "Cleaning..."
	rm -vf ${PROGNAME}
	@echo "Done!"
