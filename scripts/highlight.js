document.addEventListener('DOMContentLoaded', function () {
    const editor = ace.edit("editor");
    editor.setTheme("ace/theme/monokai");
    editor.setReadOnly(true);
    editor.setShowPrintMargin(false);
    editor.renderer.setShowGutter(true);

    const code = editor.getValue();
    const detectedLang = detectLanguage(code);
    editor.session.setMode("ace/mode/" + detectedLang);

    const themeSelector = document.getElementById('themeSelector');
    themeSelector.addEventListener('change', function (e) {
        editor.setTheme("ace/theme/" + e.target.value);
    });


    function createSafeRegex(pattern) {
        try {
            return new RegExp(pattern);
        } catch (e) {
            console.error('Invalid regex pattern:', pattern);
            return null;
        }
    }

    function detectLanguage(code) {
        const patterns = {
            php: '^<\\?php|<\\?=',
            html: '^<!DOCTYPE|^<html|^<\\w+>|<\\/\\w+>',
            css: '^(\\.|#|@media|@import|[a-z0-9-]+\\s*{)',
            python: '^(import\\s+|from\\s+\\w+\\s+import|def\\s+\\w+|class\\s+\\w+)',
            javascript: '^(const|let|var|function|import\\s+{|export|async|class\\s+\\w+)',
            java: '^(public\\s+class|package\\s+|import\\s+java)',
            csharp: '^(using\\s+System|namespace|public\\s+class)',
            golang: '^(package\\s+main|import\\s+"|\\s*func\\s+main)',
            sql: '^(SELECT|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER|WITH|BEGIN|DECLARE)',
            ruby: '^(require|module|class|def\\s+|gem\\s+\')',
            c_cpp: '^(#include|using namespace|int main|void main)',
            sh: '^(#!/bin/|#!/usr/bin/env)'
        };

        for (const [lang, pattern] of Object.entries(patterns)) {
            const regex = createSafeRegex(pattern);
            if (regex && regex.test(code)) {
                return lang;
            }
        }

        const markers = {
            javascript: ['console.log', 'document.', '=>'],
            python: ['print(', 'def ', 'if __name__ == '],
            java: ['System.out.println', 'public static void', 'extends '],
            c_cpp: ['std::', 'cout <<', '->']
        };

        const scores = {};
        for (const [lang, signs] of Object.entries(markers)) {
            scores[lang] = signs.filter(sign => code.includes(sign)).length;
        }

        const maxLang = Object.entries(scores).reduce((a, b) =>
            b[1] > a[1] ? b : a, ['text', 0]);

        return maxLang[1] > 0 ? maxLang[0] : 'text';
    }
});