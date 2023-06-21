clean:
	@echo "clean target not set up yet"
	find . -type f -name '*~' -exec ls {} \;

verstest:
	echo "making version"
	bin/version-gen
	echo "new version file"
	cat version.php
	echo "Version is: $(VERS)"

version:
	echo "making version"
	bin/version-gen
	echo "new version file"
	@ cat public_html/version.php

	git commit -m "Updating version number to $(VERS)" public_html/version.php
	git push