#!/bin/sh
# Runs the smoke cases with the php binary on PATH. Exits non-zero on failure.
# Usually invoked through tests/run.sh, which runs this inside php docker images.

cd "$(dirname "$0")/.." || exit 1

failures=0

# check <path_info> <expected substring> [front controller]
# runs from the front controller's directory, as a web server would
check() {
  front=${3:-tests/index.php}
  out=$(cd "$(dirname "$front")" && PATH_INFO="$1" php -d display_errors=1 "$(basename "$front")" 2>&1)
  if printf '%s' "$out" | grep -qF -- "$2" \
    && ! printf '%s' "$out" | grep -qiE 'fatal error|deprecated|warning:|notice:'; then
    echo "  pass  $1"
  else
    echo "  FAIL  $1 (expected: $2)"
    printf '%s\n' "$out" | sed 's/^/        /' | head -20
    failures=$((failures + 1))
  fi
}

echo "PHP $(php -r 'echo PHP_VERSION;')"

for f in $(find htdocs tinymvc tests -name '*.php'); do
  php -l "$f" > /dev/null 2>&1 || { php -l "$f"; failures=$((failures + 1)); }
done

check / 'Welcome to TinyMVC!' htdocs/index.php
check / 'VIEW hello world 0.'
check /routed 'ROUTED OK'
check /index 'VIEW hello world 0.'
check /index 'Welcome to TinyMVC!' htdocs/index.php
check /smoke/db 'DB OK'
check /smoke/uri/a/1/b/2 'URI OK'
check /smoke/helper 'HELPER OK'
check /smoke/warning '<b>Message:</b> Undefined'
check /smoke/_check "Action name not allowed"
check '/smoke/<img src=x onerror=alert(1)>' 'Unknown controller method &#039;&lt;img'

if [ "$(php -r 'echo PHP_MAJOR_VERSION;')" -ge 7 ]; then
  check /smoke/fatal '<b>Message:</b> engine error'
fi

exit $failures
