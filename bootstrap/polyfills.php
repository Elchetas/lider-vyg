<?php

/**
 * Minimal mbstring polyfills.
 *
 * Laravel expects the mbstring extension to be available. Some environments
 * (shared hosting / stripped CLI images) ship PHP without mbstring enabled,
 * which causes hard failures (e.g. mb_split not found).
 *
 * These polyfills aim to keep the app bootable without ext-mbstring. They are
 * NOT a full replacement for mbstring; for best correctness, enable mbstring.
 */

// phpcs:ignoreFile

if (!function_exists('mb_internal_encoding')) {
    function mb_internal_encoding(?string $encoding = null): string|bool
    {
        static $enc = 'UTF-8';

        if ($encoding === null) {
            return $enc;
        }

        $enc = $encoding;
        return true;
    }
}

if (!function_exists('mb_regex_encoding')) {
    function mb_regex_encoding(?string $encoding = null): string|bool
    {
        // Keep a separate store for regex encoding, like mbstring.
        static $enc = 'UTF-8';

        if ($encoding === null) {
            return $enc;
        }

        $enc = $encoding;
        return true;
    }
}

if (!function_exists('mb_detect_encoding')) {
    function mb_detect_encoding(string $string, array|string|null $encodings = null, bool $strict = false): string
    {
        // Best effort: assume UTF-8 (Laravel defaults to UTF-8).
        return 'UTF-8';
    }
}

if (!function_exists('mb_strlen')) {
    function mb_strlen(string $string, ?string $encoding = null): int
    {
        $encoding = $encoding ?: (is_string(mb_internal_encoding()) ? mb_internal_encoding() : 'UTF-8');

        if (function_exists('iconv_strlen')) {
            $len = @iconv_strlen($string, $encoding);
            if ($len !== false) {
                return $len;
            }
        }

        // Fallback: count Unicode codepoints via regex.
        if (preg_match_all('/./us', $string, $m) !== false) {
            return count($m[0]);
        }

        return strlen($string);
    }
}

if (!function_exists('mb_substr')) {
    function mb_substr(string $string, int $start, ?int $length = null, ?string $encoding = null): string
    {
        $encoding = $encoding ?: (is_string(mb_internal_encoding()) ? mb_internal_encoding() : 'UTF-8');

        if (function_exists('iconv_substr')) {
            $res = @iconv_substr($string, $start, $length ?? (mb_strlen($string, $encoding) - $start), $encoding);
            if ($res !== false) {
                return $res;
            }
        }

        // Regex fallback.
        $pattern = '/^.{0,' . max(0, $start) . '}(.{0,' . ($length ?? 1000000000) . '}).*$/us';
        if (preg_match($pattern, $string, $m) === 1) {
            return $m[1];
        }

        return $length === null ? substr($string, $start) : substr($string, $start, $length);
    }
}

if (!function_exists('mb_strtolower')) {
    function mb_strtolower(string $string, ?string $encoding = null): string
    {
        // Best effort without intl: works perfectly for ASCII, and often OK for UTF-8.
        return strtolower($string);
    }
}

if (!function_exists('mb_strtoupper')) {
    function mb_strtoupper(string $string, ?string $encoding = null): string
    {
        return strtoupper($string);
    }
}

if (!function_exists('mb_strpos')) {
    function mb_strpos(string $haystack, string $needle, int $offset = 0, ?string $encoding = null): int|false
    {
        // Byte-based; good enough for most framework internals.
        return strpos($haystack, $needle, $offset);
    }
}

if (!function_exists('mb_strrpos')) {
    function mb_strrpos(string $haystack, string $needle, int $offset = 0, ?string $encoding = null): int|false
    {
        return strrpos($haystack, $needle, $offset);
    }
}

if (!function_exists('mb_substr_count')) {
    function mb_substr_count(string $haystack, string $needle, ?string $encoding = null): int
    {
        return substr_count($haystack, $needle);
    }
}

if (!function_exists('mb_split')) {
    function mb_split(string $pattern, string $string, int $limit = -1): array
    {
        // mb_split uses an ERE-style pattern. Laravel calls it with patterns like "\s+".
        // We'll treat the pattern as a PCRE pattern and add UTF-8 + Unicode flags.
        $delim = '/';
        $pcre = $delim . $pattern . $delim . 'u';

        $flags = PREG_SPLIT_NO_EMPTY;
        if ($limit === 0) {
            $limit = -1;
        }

        $res = @preg_split($pcre, $string, $limit, $flags);
        return is_array($res) ? $res : [$string];
    }
}
