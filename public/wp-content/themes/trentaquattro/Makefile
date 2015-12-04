#
# Copyright (C) 2014 Camelidae Group SPRL
#
# This file is part of SilexSkeleton.
#
# SilexSkeleton is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# SilexSkeleton is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with SilexSkeleton. If not, see <http://www.gnu.org/licenses/>.
#

#-----------------------------------------------------------------------
# Build parameters
#-----------------------------------------------------------------------

# Parameters
rev = 
SHELL = /bin/bash
ifndef env
	ifdef UNT_MAKE_DEFAULT_ENV
		env = $(UNT_MAKE_DEFAULT_ENV)
	else
		env = debug
	endif
endif

# Environment
read = read -ep
read_s = read -sep

web_dist = $(notdir $(wildcard web/dist/*))

install_dirs = app/config/local static/content var/log var/run/sessions var/files/covers

#-----------------------------------------------------------------------
# Commands
#-----------------------------------------------------------------------

default: build

help:
	@echo "UNT build utility"
	@echo
	@echo "  make [build]           : build everything"
	@echo "  make install 			: install this app"
	@echo "  make clean             : clean built objects"
	@echo "  make purge             : remove uploaded files"
	@echo "  make distclean         : clean, purge and remove configuration files"
	@echo "  make reset             : clean, purge, build, install"
	@echo

build: web

clean: clean/web

clean/web:
	@echo -n "Cleaning web app..."
	@for f in $(web_dist); do \
		test -e "static/$$f" && rm -rf "static/$$f" || true; \
	done
	@(cd web && $(MAKE) env=$(env) SHELL=$(SHELL) clean 1>/dev/null)
	@echo "done."

clean/sessions:
	@for dir in $(install_dirs); do \
		test -d $$dir || mkdir -p $$dir; \
	done
	@sess_count="$$(ls -1 var/run/sessions | wc -l)"; \
	if [[ "$$sess_count" -lt 1 ]]; then \
		echo "No sessions registered"; \
	else \
		echo -n "Cleaning "$$sess_count" session(s)..."; \
		rm var/run/sessions/*; \
		echo "done."; \
	fi

install: clean/sessions
	@for dir in $(install_dirs); do \
		test -d $$dir || mkdir -p $$dir; \
	done


distclean: purge
	@echo -n "Removing configuration files......"
	@test "$$(ls -1 app/config/local | wc -l)" -gt 0 && rm -rf app/config/local/* || true
	@test "$$(ls -1 app/res/install | wc -l)" -gt 0 && rm -rf app/res/install/* || true
	@echo "done."

purge:
	@echo -n "Purging content files..."
	@test "$$(ls -1 static/content | wc -l)" -gt 0 && rm -rf static/content/* || true
	@echo "done."

reset: clean purge build install

.PHONY: default help build install clean clean/web clean/sessions purge distclean reset

#-----------------------------------------------------------------------
# Targets
#-----------------------------------------------------------------------

web: web/prepare
	@$(MAKE) -s env=$(env) SHELL=$(SHELL) web/import

#
# Note: the addprefix/wildcard stuff should be processed after the app
# have been prepared (to determine what are the distributed files).
#
web/import: $(addprefix static/, $(web_dist))

.PHONY: web web/import

#-----------------------------------------------------------------------
# Rules for web app
#-----------------------------------------------------------------------

# $@: target
# $*: target basename
# $<: first dep
# $^: all deps
# $?: more recent deps

web/prepare:
	@echo -n "Preparing web app..."
	@test -d static || mkdir -p static
	@(cd web && ( \
		$(MAKE) env=$(env) SHELL=$(SHELL) 1>/dev/null 2>/dev/null \
			&& echo "done." \
			|| (echo -e "\033[0;31mfailed!\033[0m"; exit 1); \
	))

static/%: web/dist/%
	@echo -n "Importing $@ from web..."
	@[[ ! -e "$@" && ! -L "$@" ]] || rm -rf "$@"
	@test "$(env)" = "debug" && ln -sr "$<" "$@" || true
	@test "$(env)" = "release" && cp -r "$<" "$@" || true
	@echo "done."

.PHONY: web/prepare
