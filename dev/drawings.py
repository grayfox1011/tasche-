#!/usr/bin/env python3
"""The three small drawings over the prices of a dish on tablets and phones, as in the Figma:
the pocket with its arrow, the bowl "M" with its arrow, the bowl "L".

    python3 dev/drawings.py <folder with menu-arrow-green.svg and menu-arrow-bordeaux.svg>

Writes assets/svg/menu-telefono-tasca.svg, -media.svg and -grande.svg. The places are those of the Figma's card
(358 px wide): each drawing is cut to its own box, to be centred in a third of the card. The letters M and L are
outlines of DM Sans Black, so the pictures need no font. Needs fontTools and brotli (only to run this script).
"""
import os
import re
import sys

from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.transformPen import TransformPen
from fontTools.ttLib import TTFont
from fontTools.varLib.instancer import instantiateVariableFont

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
SVG = os.path.join(ROOT, 'assets', 'svg')
SRC = sys.argv[1]
GREEN, BORDEAUX, LETTER = '#4E7555', '#864B52', '#89515E'


def inner(path, colour=None):
    """What is inside an <svg>, its viewBox, and its strokes in another colour."""
    text = open(path, encoding='utf8').read()
    box = [float(n) for n in re.search(r'viewBox="([^"]+)"', text).group(1).split()]
    body = re.search(r'<svg[^>]*>(.*)</svg>', text, re.S).group(1)
    if colour:
        body = re.sub(r'stroke="#[0-9A-Fa-f]{6}"', 'stroke="%s"' % colour, body)
    body = re.sub(r'\s(?:id|clip-path)="[^"]*"', '', body)
    body = re.sub(r'<defs>.*?</defs>', '', body, flags=re.S)
    return box, body.strip()


def placed(path, x, y, w, h, colour=None):
    box, body = inner(path, colour)
    return '<svg x="%g" y="%g" width="%g" height="%g" viewBox="%g %g %g %g" fill="none">%s</svg>' % (x, y, w, h, box[0], box[1], box[2], box[3], body)


def letter(font, char, x, top, size, colour):
    """A letter of DM Sans Black as a path: `top` is the top of its line, as high as the font's size."""
    glyphs = font.getGlyphSet()
    name = font.getBestCmap()[ord(char)]
    scale = size / font['head'].unitsPerEm
    hhea = font['hhea']
    # A line as high as the size: the letters stand in the middle of it, as a browser puts them.
    baseline = top + (size - (hhea.ascent - hhea.descent) * scale) / 2 + hhea.ascent * scale
    pen = SVGPathPen(glyphs)
    glyphs[name].draw(TransformPen(pen, (scale, 0, 0, -scale, x, baseline)))
    return '<path d="%s" fill="%s"/>' % (pen.getCommands(), colour)


def write(name, x0, y0, x1, y1, parts):
    w, h = round(x1 - x0, 2), round(y1 - y0, 2)
    out = '<svg xmlns="http://www.w3.org/2000/svg" width="%g" height="%g" viewBox="%g %g %g %g" fill="none">\n%s\n</svg>\n' % (w, h, x0, y0, w, h, '\n'.join(parts))
    open(os.path.join(SVG, name), 'w', encoding='utf8', newline='\n').write(out)
    print('%s: %g x %g' % (name, w, h))


font = TTFont(os.path.join(ROOT, 'assets', 'fonts', 'tp-dm-sans-opsz-normal.woff2'))
font = instantiateVariableFont(font, {'wght': 900, 'opsz': 17})

# The Figma's places, from the card's left top corner.
write('menu-telefono-tasca.svg', 29, 107, 79, 186, [
    placed(os.path.join(SRC, 'menu-arrow-green.svg'), 63.52, 108.66, 13.8125, 32.6172),
    placed(os.path.join(SVG, 'menu-tasca-icon.svg'), 30.74, 131.68, 51.46, 53.27, GREEN),
])
write('menu-telefono-media.svg', 141, 123, 207, 186, [
    placed(os.path.join(SRC, 'menu-arrow-bordeaux.svg'), 142.49, 124.23, 12.605, 34.5391),
    placed(os.path.join(SVG, 'menu-piatto-m-icon.svg'), 150.74, 143.66, 40.56, 40.56, BORDEAUX),
    letter(font, 'M', 189.7, 156.6, 17.42, LETTER),
])
write('menu-telefono-grande.svg', 270, 138, 331, 186, [
    placed(os.path.join(SVG, 'menu-piatto-l-icon.svg'), 271.74, 139.39, 44.83, 44.83, BORDEAUX),
    letter(font, 'L', 319.7, 156.6, 17.42, LETTER),
])
