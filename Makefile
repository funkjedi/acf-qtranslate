
REPO_URL=http://plugins.svn.wordpress.org/acf-qtranslate
WORKING_DIR=/tmp/acf-qtranslate-svn
CURRENT_VERSION=$(shell perl -nle'print $$& if m{(?<=Version: )([0-9]|\.)*(?=\s|$$)}' acf-qtranslate.php)

all:
	@echo "Usage:"
	@echo
	@echo "  [make sync]"
	@echo "  Commits all the latest changes from Github to the Wordpress Plugin Directory."
	@echo
	@echo "  [make release]"
	@echo "  Creates new release in the Wordpress Plugin Directory and tags release on Github."
	@echo

sync:
	@rm -rf $(WORKING_DIR)
	@svn co $(REPO_URL) $(WORKING_DIR)
	@echo "Copying files to trunk"
	@git ls-tree -r --name-only HEAD | xargs -t -I filename rsync -R --exclude 'Makefile' filename $(WORKING_DIR)/trunk/
	@cd $(WORKING_DIR); svn add trunk/* --force
	@cd $(WORKING_DIR); svn status
	@cd $(WORKING_DIR); svn commit -m "Syncing with Github"
	@rm -rf $(WORKING_DIR)

release:
	@echo "Creating release tag"
	@svn copy $(REPO_URL)/trunk/ $(REPO_URL)/tags/$(CURRENT_VERSION) -m "Tagging version $(CURRENT_VERSION)"
	@git tag -a -m "Tagging version $(CURRENT_VERSION)" "v$(CURRENT_VERSION)"
	@git push --tags origin master
