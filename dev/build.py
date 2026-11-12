#!/usr/bin/env python3
"""Writes the pages of the site as Divi 5 blocks: content/home.html, content/header.html, content/footer.html.

    python3 dev/build.py

The theme imports those files the first time (inc/setup-site.php). After that the pages live in
WordPress and are changed from Divi: this script is only the way they were first written.

{{media:path}} stands for a file of assets/: the import puts it in the Media Library and writes its address.
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from divi import *  # noqa: E402,F403
import texts as T  # noqa: E402

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

# For checks only: TP_TEXTS=<another texts.py> builds the same pages with other words and pictures (those of
# version 0.8.3, to lay the result over that version), TP_OUT=<folder> writes them elsewhere than content/.
if os.environ.get('TP_TEXTS'):
    import importlib.util
    _spec = importlib.util.spec_from_file_location('texts_other', os.environ['TP_TEXTS'])
    T = importlib.util.module_from_spec(_spec)
    _spec.loader.exec_module(T)
OUT = os.environ.get('TP_OUT') or os.path.join(ROOT, 'content')

# Colours and fonts of Tasche Piene.
CREAM = '#fff5e9'
PAPER = '#f7eada'      # the darker cream of the menu rows and of the cards
GREEN = '#054726'
BORDEAUX = '#560b22'
BROWN = '#3e372e'
LINE = '#c5b197'
WHITE = '#ffffff'
INK = '#222222'
DISPLAY = 'Bowlby One'
BODY = 'DM Sans'

# The measures are those of the Figma (a 1512 px frame for computers, a 390 px one for phones), as version 0.8.3
# drew them. A length that must follow the screen is written as a share of 1512: vw(96) is 96 px on that frame.
def vw(px, least=None, most=None):
    share = '%svw' % round(px / 15.12, 3)
    if least is None and most is None:
        return share
    return 'clamp(%spx, %s, %spx)' % (least if least is not None else 0, share, most if most is not None else px)


# The page is as wide as the screen less 30 px on each side (16 on phones), up to 1452 px.
WIDE = sizing(width=R('calc(100% - 60px)', tablet='calc(100% - 64px)', phone='calc(100% - 32px)'), maxWidth='1452px')
# The header's bar and the hero's words stand 50 px from the sides.
INSET = sizing(width=R('calc(100% - 100px)', tablet='calc(100% - 64px)', phone='calc(100% - 30px)'), maxWidth='1412px')
FULL = sizing(width='100%', maxWidth='100%')
WIDTH_NARROW = R('calc(100% - 60px)', tablet='calc(100% - 64px)', phone='calc(100% - 32px)')
ARROW = {'unicode': '&#x26;', 'type': 'divi', 'weight': '400'}
# Divi leaves 10 px under every title: taken back where a title sits beside something else.
UNDER = spacing(margin=box('0px', '0px', '-10px', '0px'))


def media(path):
    return '{{media:%s}}' % path


def display_font(size, color, line='1.04em', **more):
    return dict(family=DISPLAY, weight='400', size=size, lineHeight=line, color=color, **more)


def body_font(size, color, weight='500', line='1.4em', **more):
    return dict(family=BODY, weight=weight, size=size, lineHeight=line, color=color, **more)


def cta(name, label_, url, ink, paper, *attributes, target='off', plain_on_phone=False, phone_side='24px'):
    """The button with the arrow: `ink` the text, `paper` the fill. On computers 54 px high with a 20 px text, as
    in the Figma. `plain_on_phone`: on phones the small button of the Figma, the name in Bowlby One and no arrow."""
    font = dict(family=BODY, weight='700', size=R('20px', phone='18px'), lineHeight='1.3em', color=R(ink, hover=paper))
    # Figma: the words 25 px from the left side; to their right the arrow, in the middle of the last 54 px (48 on
    # phones, where the arrow stands further from the words). tp-freccia: the theme draws the Figma's arrow in
    # place of Divi's thin one (assets/css/tp.css).
    pad = box(R('12px', phone='10px'), R('52px', phone='61px'), R('12px', phone='10px'), R('23.5px', phone='21px'))
    rad = R('15px', phone='13px')
    if plain_on_phone:
        font.update(family=R(BODY, phone=DISPLAY), weight=R('700', phone='400'), size=R('20px', phone='16px'), lineHeight=R('1.3em', phone='1.56em'))
        pad = box(R('12px', phone='5px'), R('52px', phone=phone_side), R('12px', phone='5px'), R('23.5px', phone=phone_side))
        rad = R('15px', phone='8px')
    return button(
        name, label_, url,
        at('button.decoration.background', {'color': R(paper, hover=ink)}),
        border(rad=rad, width='2px', color=paper, element='button'),
        spacing(padding=pad),
        at('module.advanced.alignment', 'left'),
        attrs(cls='tp-freccia'),
        *attributes,
        target=target, icon=ARROW, icon_on=R('on', phone='off') if plain_on_phone else 'on',
        **font,
    )


# ---------------------------------------------------------------------------------------------
# Pieces used more than once.

H2 = R(vw(64, 40, 64), tablet='40px', phone='32px')                         # a section's title
BIG = R(vw(96, 46, 96), tablet='56px', phone='30px')                        # the two largest titles
COPY = R('20px', tablet='19px', phone='18px')


def title(name, words, level, color, *attributes, size=H2, upper_on_phone=True, line=None, **more):
    props = display_font(size, color, line=line or R('1em', phone='1.0023em'), **more)
    if upper_on_phone:
        props['capitalization'] = R('none', phone='uppercase')
    return heading(name, words, level, UNDER, *attributes, **props)


def copy(name, html, color, *attributes, **more):
    return text(name, html, *attributes, **merge(body_font(COPY, color), more))


def picture(name, item, *attributes, rad='20px', **options):
    """A photo: `item` has src (a file of assets/), alt and title. Options as in divi.image."""
    return image(name, media(item['src']), item.get('alt', ''), *attributes, title=item.get('title') or None, rad=rad, **options)


def rise(delay='0ms'):
    """Design > Animation: the piece comes up into place when it enters the screen."""
    return animation(style='slide', direction='bottom', duration='800ms', delay=delay, intensity='6%', curve='ease-out')


def wave(name, computer, phone=None, *attributes):
    """A wave between two sections. With `phone`, phones have their own picture."""
    pictures = [image(name + (' (computer e tablet)' if phone else ''), media(computer), '', *([hide_on(phone=True)] if phone else []),
                      full=True, bottom_space=False)]
    if phone:
        pictures.append(image(name + ' (telefono)', media(phone), '', hide_on(desktop=True, tablet=True), full=True, bottom_space=False))
    return row(name, [column(pictures, layout(direction='column', gap='0px'))], FULL, *attributes)


# ---------------------------------------------------------------------------------------------
# Home.

def hero():
    # Figma: the screen is 982 px high; the title is 96/100, the button 54 px high, the last line 24/26.
    # On phones: title 32/38 at 350 px from the top, button, last line 19 px; the section is 812 px high.
    return section(
        'Hero', [
            row('Hero: testi', [
                column([
                    text('Titolo', '<h1>%s</h1>' % T.HERO_TITLE_HTML, attrs(cls='tp-lettere'),
                         headings={'h1': display_font(R(vw(96, 48, 96), tablet='56px', phone='32px'), CREAM,
                                                      line=R('1.0417em', phone='1.1875em'))}),
                    cta('Pulsante', T.HERO_BUTTON, '#menu', BORDEAUX, CREAM,
                        spacing(margin=box(R('39px', phone='15px'), '0px', '0px', '0px'))),
                    text('Sottotitolo', '<p>%s</p>' % T.HERO_SUBLINE,
                         spacing(margin=box(R('73px', phone='98px'), '0px', '0px', '0px')), sizing(maxWidth=R('none', phone='250px')),
                         **body_font(R('24px', tablet='21px', phone='19px'), CREAM, weight=R('500', phone='400'), line=R('1.0833em', phone='1em'))),
                ], layout(direction='column', gap='0px', align='stretch')),
            ], INSET),
        ],
        attrs(id='home', cls='tp-video-telefono'),
        background(color=INK, image={'url': media('images/hero-poster.webp'), 'size': 'cover', 'position': 'center', 'repeat': 'no-repeat'},
                   video={'mp4': media('video/videodefinitivo.mp4'), 'webm': media('video/videodefinitivo.webm'), 'width': '1920', 'height': '1080',
                          'allowPlayerPause': 'off', 'pauseOutsideViewport': 'on'}),
        sizing(minHeight=R('100vh', phone='812px')),
        layout(direction='column', justify='flex-end', align='center'),
        spacing(padding=box(R('150px', phone='120px'), '0px', R('47px', phone='110px'), '0px')),
    )


def strip():
    # Figma: a green band 77 px high with 24 px words; on phones 34 px with 11.5 px words.
    return section(
        'Striscia che scorre', [
            row('Striscia', [
                column([
                    # data-durata: the seconds the whole text takes to go by once (as on the site before this version).
                    text('Scritta che scorre', '<p>%s</p>' % T.STRIP,
                         attrs(cls='tp-scorre', data_durata='10'),
                         **display_font(R('24px', tablet='18px', phone='11.5px'), CREAM, line='1em')),
                ]),
            ], FULL),
        ],
        background(color=GREEN),
        spacing(padding=box(R('26.5px', tablet='18px', phone='11.5px'), '0px', R('26.5px', tablet='18px', phone='11px'), '0px')),
    )


def intro():
    # Figma (1512): the photo 459 x 684, turned 4 degrees, stands 30 px from the left; the words start at 654 px
    # and are 701 px wide: title 64/64, text 20/28, button. On phones: title, text, button, photo.
    d = T.INTRO
    return section(
        'Introduzione', [
            row('Introduzione', [
                column([
                    group('Foto', [
                        picture('Foto', d['image'], attrs(cls='tp-foto'), transform(rotate=R('-4deg', tablet='0deg')), width=R('91.07%', tablet='100%'),
                                aspect=R((459, 684), tablet=(4, 3)), rad='18px', align='center',
                                margin=box(R(vw(15), tablet='0px'), 'auto', R(vw(15), tablet='0px'), 'auto')),
                    ], layout(direction='column', align='stretch'), sizing(width=R('34.71%', tablet='100%'), flexShrink='0')),
                    group('Testi', [
                        title('Titolo', d['title'], 'h2', BORDEAUX),
                        copy('Testo', d['html'], BORDEAUX, spacing(margin=box(R('17px', phone='22px'), '0px', '0px', '0px'))),
                        cta('Pulsante', d['button'], d['button_url'], CREAM, BORDEAUX, spacing(margin=box(R('35px', phone='20px'), '0px', '0px', '0px')),
                            plain_on_phone=True),
                    ], layout(direction='column', gap='0px', align='stretch'), spacing(padding=box(R(vw(6), tablet='0px'), '0px', '0px', '0px')),
                        sizing(width=R('48.28%', tablet='100%'), flexShrink='0')),
                ], layout(direction=R('row', tablet='column-reverse'), gap=R('8.26%', tablet='19px'), justify='flex-start',
                          align=R('flex-start', tablet='stretch'), wrap='nowrap')),
            ], WIDE),
        ],
        attrs(id='chi-siamo'),
        background(color=CREAM),
        spacing(padding=box(R(vw(127), tablet='72px', phone='63px'), '0px', R('48px', phone='24px'), '0px')),
    )


def alberto():
    # Figma (1512): the words start at 155 px and are 702 px wide; the two photos, turned, stand on the right, the
    # small one a little over the large one. On phones: title, text and the two photos, one under the other.
    d = T.ALBERTO
    return section(
        'Alberto', [
            row('Alberto', [
                column([
                    group('Testi', [
                        title('Titolo', d['title'], 'h2', GREEN, line=R('1.0266em', phone='1.0023em')),
                        copy('Testo', d['html'], GREEN, spacing(margin=box(R('24px', phone='19px'), '0px', '0px', '0px')),
                             sizing(maxWidth=R('92.4%', tablet='100%')), lineHeight=R('1.39em', phone='1.42em')),
                    ], layout(direction='column', gap='0px', align='stretch'),
                        spacing(padding=box(R(vw(23), tablet='0px'), '0px', '0px', '0px'), margin=box('0px', '0px', '0px', R('8.61%', tablet='0px'))),
                        sizing(width=R('52.34%', tablet='100%'), flexShrink='0')),
                    group('Foto', [
                        picture('Foto in alto', d['photo_sub'], attrs(cls='tp-foto'), transform(rotate=R('5.14deg', tablet='0deg')), z_index('3'),
                                border(rad='20px', width=R('6px', tablet='0px'), color=CREAM, element='image'),
                                width=R('65.5%', tablet='100%'), aspect=R((359, 239), tablet=(3, 2)),
                                margin=box(R(vw(15.5), tablet='0px'), '0px', R(vw(-28.5), tablet='0px'), R('23%', tablet='0px'))),
                        picture('Foto in basso', d['photo_main'], attrs(cls='tp-foto'), transform(rotate=R('-3.9deg', tablet='0deg')), z_index('2'),
                                width=R('95.8%', tablet='100%'), aspect=R((525, 350), tablet=(3, 2)),
                                margin=box('0px', '0px', R(vw(45), tablet='0px'), R('2.1%', tablet='0px')), rad='20px'),
                    ], layout(direction=R('column', tablet='column-reverse'), gap='0px', align=R('flex-start', tablet='stretch')),
                        spacing(margin=box('0px', R('1.38%', tablet='0px'), '0px', '0px')),
                        sizing(width=R('37.74%', tablet='100%'), flexShrink='0')),
                ], layout(direction=R('row', tablet='column'), gap=R('0px', tablet='26px'), justify='space-between',
                          align=R('flex-start', tablet='stretch'), wrap='nowrap')),
            ], WIDE),
        ],
        background(color=CREAM),
        spacing(padding=box(R(vw(98), tablet='40px', phone='8px'), '0px', R('0px', tablet='40px', phone='0px'), '0px')),
    )


def chip_list(name, phrases, paper, icons):
    """The little labels of a card: an Icon List whose items are pills (23 px high on computers; tiny on phones,
    as in the Figma, where the three of them fit in one line)."""
    items = []
    for phrase, glyph in zip(phrases, icons):
        items.append(block(
            'divi/icon-list-item', label('Etichetta: ' + phrase),
            at('content.innerContent', phrase),
            at('icon.innerContent', {'unicode': glyph, 'type': 'fa', 'weight': '900'}),
            background(color=paper),
            border(rad=R('10px', phone='5px')),
            spacing(padding=box(R('1px', phone='0px'), R('12px', phone='6px'))),
            layout(direction='row', gap=R('6px', phone='4px'), align='center'),
        ))
    return block(
        'divi/icon-list', label(name),
        at('icon.advanced.color', CREAM), at('icon.advanced.size', R('14px', phone='7px')),
        at('listItem.decoration.font.font', display_font(R(vw(14, 11, 14), tablet='12px', phone='7.5px'), CREAM, line='1.5em')),
        layout(direction='row', gap=R('10px', phone='5px'), wrap='wrap', align='center'),
        children=items,
    )


def choice_card(d, ink, icons, delay='0ms'):
    # Figma (1512): a cream card 716 x 581, the photo 630 x 358 at 33 px from the left and 47 from the top, the name
    # in Bowlby One 37.5 px, the labels. On phones 358 x 291 with the photo 315 x 179.
    # tp-scheda: with the mouse over the card (a tap on phones) the photo (tp-scheda-foto) fills it and what has
    # tp-scheda-nascosto, the description, comes up over it. In the Visual Builder everything is in view, to be written.
    return column([
        picture('Foto', d['image'], attrs(cls='tp-scheda-foto tp-foto'), width='96.92%', aspect=R((630, 358), phone=(315, 179)),
                rad=R('18px', phone='9px')),
        title('Nome', d['name'], 'h3', ink, spacing(margin=box(R(vw(30, 18, 30), phone='8px'), '0px', '-10px', '0px')),
              size=R(vw(37.5, 26, 37.5), tablet='28px', phone='18.75px'), line='1.002em', upper_on_phone=False),
        chip_list('Etichette', d['chips'], ink, icons),
        text('Descrizione (compare al passaggio del mouse)', d['html'], attrs(cls='tp-scheda-nascosto'),
             **body_font(R('16px', phone='11px'), ink, weight='400', line=R('1.5em', phone='1.22em'))),
    ], attrs(cls='tp-scheda'), background(color=CREAM), border(rad=R('20px', phone='10px')),
        spacing(padding=box(R(vw(47, 30, 47), phone='23.5px'), R(vw(33, 22, 33), phone='16.6px'), R('14px', phone='23px'), R(vw(33, 22, 33), phone='16.6px'))),
        layout(direction='column', gap=R('19px', phone='15px')), rise(delay), size=R('12_24', tablet='24_24'), name='Scheda: ' + d['name'])


def choice():
    # Figma (1512): the wave starts 53 px inside the section before; the title, 96/107, stands on the lower part
    # of the wave; the two cards are 30 px from the sides.
    d = T.SCELTA
    return section(
        'Tasca o vaschetta', [
            wave('Onda', 'svg/onda-crema-bordeaux.svg', 'svg/onda-crema-bordeaux-telefono.svg'),
            row('Titolo', [
                column([
                    text('Titolo', '<h2>%s</h2>' % d['title_html'], attrs(cls='tp-lettere'),
                         headings={'h2': display_font(BIG, R(WHITE, phone=CREAM), line=R('1.1125em', phone='1.0023em'))}),
                ]),
            ], WIDE, spacing(margin=box(R(vw(-132), tablet='-4vw', phone='-17px'), 'auto', '0px', 'auto'))),
            row('Schede', [
                choice_card(d['cards'][0], GREEN, ['&#xf70e;', '&#xf2e7;', '&#xf805;']),
                choice_card(d['cards'][1], BORDEAUX, ['&#xf24e;', '&#xf4d8;', '&#xf7ec;'], delay='120ms'),
            ], WIDE, layout(direction=R('row', tablet='column'), gap=R('20px', phone='9px'), align='stretch', wrap='nowrap'), columns=2,
                *[spacing(margin=box(R(vw(76), tablet='48px', phone='40px'), 'auto', '0px', 'auto'))]),
            # On phones the menu is on the cream page (Figma): the wave that leaves the bordeaux comes here, after the cards.
            row('Onda (telefono)', [
                column([
                    image('Onda (telefono)', media('svg/onda-bordeaux-crema-telefono.svg'), '', full=True, bottom_space=False),
                ]),
            ], FULL, hide_on(desktop=True, tablet=True), spacing(margin=box('-12px', 'auto', '0px', 'auto'))),
        ],
        attrs(id='come-mangi'),
        background(color=BORDEAUX),
        layout(direction='column', gap='0px', align='center'),
        spacing(padding='0px', margin=box(R(vw(-53), tablet='0px', phone='-9px'), '0px', '0px', '0px')),
    )


# The menu. Each dish is a group one can duplicate: on computers a line of the table, on tablets and phones a card.
# Figma (1512): a cream panel 1456 px wide on the bordeaux, 19 px from the left; the names start 52 px inside it,
# the prices stand under the two boxes of the legend (165 px the pocket's, 354 px the bowl's), which end 59 px
# before the panel's right side. On phones the menu is on the cream page: cards 358 x 244 with the drawings.
CELL_TASCA = vw(165, 118, 165)       # as wide as the green box of the legend
CELL_HALF = vw(173, 124, 173)        # as wide as half the bordeaux box
CELL_GAP = '8px'
PRICE = R('20px', tablet='17.5px')
SIDE = R('58px', tablet='32px', phone='16px')        # the menu's inner side space
ROW_LEFT = '52px'
ROW_RIGHT = '59px'
SOFT_GREEN, SOFT_BORDEAUX = '#4e7555', '#864b52'     # the lighter colours of the phone's drawings


# In the Visual Builder Divi draws its tools over the piece under the mouse: a small bar along the top
# (24 px) and a "+" in the middle, some 30 px above the bottom. A short text that is all there is in its
# box ends up under them and cannot be clicked. So a price keeps the row's space inside itself, a little
# more below than above: the bar sits over it, the "+" under it, the price stays free between the two.
AIR_ABOVE = R('30px', tablet='0px')
AIR_BELOW = R('46px', tablet='0px')


def price(name, value, color, *attributes, size=PRICE):
    return text(name, '<p>%s</p>' % value, spacing(padding=box(AIR_ABOVE, '0px', AIR_BELOW, '0px')), *attributes,
                **body_font(size, color, weight='900', line='1em', textAlign='center'))


def price_table(prices):
    """Tasca, media and grande: on computers three prices under the legend. On tablets and phones the Figma's
    drawings stand over them: the pocket with its arrow and the word "Tasca", the two bowls with "Piatto".
    A grid of three columns: words and drawings share its first line, the prices are the second."""
    drawing = lambda name, path, column, margin: image(
        name, media(path), '', hide_on(desktop=True), bottom_space=False, margin=margin,
        place={'gridColumnStart': column, 'gridRowStart': '1', 'gridAlignSelf': 'end', 'gridJustifySelf': 'center'})
    word = lambda name, words, column, color, turn, margin: text(
        name, '<p>%s</p>' % words, hide_on(desktop=True), transform(rotate=turn), z_index('2'), spacing(margin=margin),
        sizing(gridColumnStart=column, gridRowStart='1', gridAlignSelf='start', gridJustifySelf='center'),
        **body_font('15.5px', color, weight='900', line='1em'))
    cells = [
        word('Parola: tasca', 'Tasca', '1', SOFT_GREEN, '-8deg', box('5px', '0px', '0px', '16px')),
        word('Parola: piatto', 'Piatto', '2', SOFT_BORDEAUX, '9deg', box('23px', '34px', '0px', '0px')),
        # The drawings are cut to their own boxes: the margins put them where the Figma has them in each third.
        drawing('Disegno: tasca', 'svg/menu-telefono-tasca.svg', '1', box('25px', '11px', '0px', '0px')),
        drawing('Disegno: media', 'svg/menu-telefono-media.svg', '2', box('0px', '10px', '0px', '0px')),
        drawing('Disegno: grande', 'svg/menu-telefono-grande.svg', '3', box('0px', '0px', '0px', '5px')),
    ]
    cells += [price('Prezzo ' + kind, value, color) for (kind, color), value in zip((('tasca', GREEN), ('media', BORDEAUX), ('grande', BORDEAUX)), prices)]
    return group('Prezzi', cells,
                 at('module.decoration.layout', {
                     'display': 'grid',
                     'gridColumnWidths': R('manual', tablet='equal'),
                     'gridTemplateColumns': '%s %s %s' % (CELL_TASCA, CELL_HALF, CELL_HALF),
                     'gridColumnCount': '3',
                     'columnGap': R(CELL_GAP, tablet='0px'),
                     'rowGap': R('0px', tablet='14px'),
                     'alignItems': 'center',
                 }),
                 sizing(flexShrink='0', width=R('auto', tablet='100%')))


def dish(d, table=True):
    """table=True: the three prices of tasca, media and grande; False: one price on the right."""
    stacked = table and len(d['prices']) == 3
    ink = '#000000' if table else BROWN
    name = [heading('Nome', d['name'], 'h3', UNDER, **body_font(R('20px', tablet='15.5px'), R(ink, tablet=BROWN), weight='900', line='1em', capitalization='uppercase'))]
    if d.get('meta'):
        # The description shows with the mouse over the line; on tablets and phones the Figma's cards have the name only.
        name.append(text('Descrizione', '<p>%s</p>' % d['meta'], attrs(cls='tp-al-passaggio'), hide_on(tablet=True, phone=True), sizing(maxWidth='425px'),
                         **body_font('15px', '#000000', weight='400', line='1em', capitalization='uppercase')))
    # The name stands on the line of the prices (see AIR_ABOVE). In a card with the drawings (tablets and phones) the
    # name starts 17 px from the card's side, while drawings and prices take the card's whole width.
    side = R('0px', tablet='17px') if stacked else '0px'
    parts = [group('Nome e descrizione', name, layout(direction='column', gap='6px', justify='flex-start'), sizing(flexGrow='1', flexShrink='1', minWidth='0px'),
                   spacing(padding=box(R('30px', tablet='0px'), side, R('20px', tablet='0px'), side)))]
    if d.get('image'):
        parts.append(image('Foto al passaggio del mouse', media(d['image']), '', attrs(cls='tp-al-passaggio tp-foto'), hide_on(tablet=True, phone=True),
                           # Figma: 284 x 379, turned 4.5 degrees, on the line's middle. Its right side stops before the
                           # prices, whatever the width of the screen.
                           position('absolute', horizontal='calc(%s + 2 * %s + 2 * %s + %s + 49px)' % (CELL_TASCA, CELL_HALF, CELL_GAP, ROW_RIGHT), origin='center right'),
                           transform(rotate='4.5deg'), z_index('20'),
                           width='clamp(210px, 18.78vw, 284px)', aspect=(284, 379), rad='10px', bottom_space=False))
    prices = d['prices']
    lone = False
    if stacked:
        parts.append(price_table(prices))
    elif table:
        # Only the tasca: its price stays in the tasca's column. On tablets and phones it goes under the name.
        parts.append(price('Prezzo tasca', prices[0], GREEN, sizing(width=R(CELL_TASCA, tablet='auto'), flexShrink='0'),
                           spacing(margin=box('0px', R('calc(2 * %s + 2 * %s)' % (CELL_HALF, CELL_GAP), tablet='0px'), '0px', '0px'))))
        lone = True
    else:
        # One price: on computers it ends where the prices of the large bowl do; on phones it is larger, as in the Figma.
        parts.append(price('Prezzo', prices[0], d.get('color', GREEN), sizing(flexShrink='0'), size=R('20px', tablet='23px')))
    if stacked:
        pad = box(R('0px', tablet='35px'), R(ROW_RIGHT, tablet='0px'), R('0px', tablet='25px'), R(ROW_LEFT, tablet='0px'))
    else:
        pad = box(R('0px', tablet='35px' if table else '23px'), R('113px' if not table else ROW_RIGHT, tablet='14px'), R('0px', tablet='30px' if table else '23px'), R(ROW_LEFT, tablet='17px'))
    return group(
        'Piatto: ' + d['name'], parts, attrs(cls='tp-piatto'),
        layout(direction=R('row', tablet='column') if stacked or lone else 'row', gap=R('30px', tablet='30px') if stacked else R('30px', tablet='12px'),
               align=R('stretch', tablet='stretch') if stacked else R('stretch', tablet='flex-start' if lone else 'center'), justify='space-between', wrap='nowrap'),
        spacing(padding=pad),
        background(color=R(CREAM, hover=PAPER, tablet='#f8ebdb')),
        border(rad=R('0px', tablet='17.4px'), sides={'bottom': {'width': '3px', 'color': LINE}, 'top': {'width': R('0px', tablet='3px'), 'color': LINE},
                                                    'left': {'width': R('0px', tablet='3px'), 'color': LINE}, 'right': {'width': R('0px', tablet='3px'), 'color': LINE}}),
    )


def dish_list(name, dishes, table=True, color=GREEN):
    rows = [dish(dict(d, color=color), table) for d in dishes]
    return group(name, rows, layout(direction='column', gap=R('0px', tablet='16px'), align='stretch'),
                 spacing(padding=box('0px', R('0px', tablet=SIDE['tablet'], phone=SIDE['phone']))),
                 border(sides={'top': {'width': R('3px', tablet='0px'), 'color': LINE}}))


def menu_head(d, color, soft):
    # Figma (1512): the two phrases in Bowlby One 32 px on one line, the second ending where the prices do.
    # On phones: the title, and under it the second phrase in DM Sans 18 px, lighter.
    return group('Titolo: ' + d['title'], [
        heading('Titolo', d['title'], 'h3', UNDER, **display_font('32px', color, line='1em')),
        text('Sottotitolo', '<p>%s</p>' % d['subtitle'], **merge(display_font(R('32px', tablet='18px'), R(color, tablet=soft), line='1em'),
                                                           {'family': R(DISPLAY, tablet=BODY), 'weight': R('400', tablet='900')})),
    ], attrs(cls='tp-entra'),  # it comes up when it is reached, as the cards of the menu do
        layout(direction=R('row', tablet='column'), gap=R('28px', tablet='11px'), justify='space-between', align=R('center', tablet='flex-start'), wrap='wrap'),
        spacing(padding=box(R('89px', tablet='72px'), R('113px', tablet=SIDE['tablet'], phone=SIDE['phone']), R('39px', tablet='28px'), R('50px', tablet=SIDE['tablet'], phone=SIDE['phone']))))


def legend():
    d = T.MENU['legend']
    small = body_font(vw(20, 14, 20), CREAM, weight='900', line='1em')
    big = display_font(vw(32, 22, 32), CREAM, line='1em')
    return group('Legenda', [
        text('Legenda: tasca', '<h4>%s</h4><p>%s</p>' % (d[0]['name'], d[0]['lines'][0]),
             background(color=GREEN), border(rad='10px'), sizing(width=CELL_TASCA, flexShrink='0'), spacing(padding=box('24px', '6px', '20px', '6px')),
             headings={'h4': merge(big, {'textAlign': 'center'})}, **merge(small, {'textAlign': 'center'})),
        group('Legenda: vaschetta', [
            text('Nome', '<h4>%s</h4>' % d[1]['name'], spacing(padding=box('21px', '0px', '0px', '0px')), headings={'h4': big}),
            group('Misure', [
                text('Misura media', '<p>%s</p>' % d[1]['lines'][0], sizing(width='52.73%'), **small),
                text('Misura grande', '<p>%s</p>' % d[1]['lines'][1], sizing(width='47.27%'), **small),
            ], layout(direction='row', gap='0px', align='flex-start', wrap='nowrap')),
            image('Personaggio', media('svg/mascot-piatto.svg'), '', position('absolute', vertical='46%', horizontal='1.5%', origin='bottom right'),
                  z_index('3'), width='28%', bottom_space=False),
        ], background(color=BORDEAUX), border(rad='10px'), overflow('visible'), sizing(width='calc(2 * %s + %s)' % (CELL_HALF, CELL_GAP), flexShrink='0'),
            spacing(padding=box('0px', '4px', '20px', '20px')), layout(direction='column', gap='0px', align='stretch', justify='center')),
    ], hide_on(tablet=True, phone=True), layout(direction='row', gap=CELL_GAP, align='stretch', wrap='nowrap'), sizing(flexShrink='0'),
        spacing(margin=box('0px', '0px', '13px', '0px')))


def menu_photo(name, item, below='18px'):
    """The photo over a list of dishes on tablets and phones (358 x 322 in the Figma)."""
    return picture(name, item, hide_on(desktop=True), align='center',
                   width=R('100%', tablet='calc(100% - 64px)', phone='calc(100% - 32px)'), aspect=R((358, 322), tablet=(2, 1), phone=(358, 322)), rad='17.4px',
                   margin=box('0px', 'auto', below, 'auto'))


def menu():
    d = T.MENU
    panel = [
        group('Testata del menù', [
            heading('Titolo', d['title'], 'h2', UNDER, attrs(cls='tp-lettere'), sizing(flexGrow='1', flexShrink='1', minWidth='0px'),
                    **display_font(R(vw(96, 46, 96), tablet='56px', phone='48px'), R(BROWN, tablet=BORDEAUX), line=R('1.0115em', tablet='1.002em'))),
            legend(),
        ], layout(direction='row', gap='32px', justify='space-between', align='flex-end', wrap=R('nowrap', tablet='wrap')),
            spacing(padding=box('0px', R(ROW_RIGHT, tablet=SIDE['tablet'], phone='15px'), R('59px', tablet='18px'), R(SIDE['desktop'], tablet=SIDE['tablet'], phone='15px')))),
        menu_photo('Foto (tablet e telefono)', d['feature'][0]),
        # The three dots of the Figma: a drawing, the photo does not change.
        image('Puntini sotto la foto (tablet e telefono)', media('svg/menu-puntini.svg'), '', hide_on(desktop=True), width='49px', bottom_space=False,
              margin=box('0px', 'auto', '26px', 'auto')),
        dish_list('Piatti', d['dishes']),
        menu_head(d['groups'][0], GREEN, SOFT_GREEN),
        dish_list('Contorni', d['groups'][0]['rows'], table=False, color=GREEN),
        menu_head(d['groups'][1], BORDEAUX, '#89515e'),
        menu_photo('Foto dei dolci (tablet e telefono)', d['feature'][1]),
        dish_list('Dolci', d['groups'][1]['rows'], table=False, color=BORDEAUX),
        menu_head(d['groups'][2], GREEN, SOFT_GREEN),
        dish_list('Da bere', d['groups'][2]['rows'], table=False, color=GREEN),
        text('Allergeni e note', d['note'], sizing(maxWidth=R('596px', tablet='100%')),
             spacing(padding=box(R('91px', tablet='65px'), R('0px', tablet=SIDE['tablet'], phone=SIDE['phone']), '0px', R('54px', tablet=SIDE['tablet'], phone=SIDE['phone']))),
             **body_font('16px', BROWN, weight='400', line='1em')),
        group('Consegna a domicilio', [
            text('Testo', '<p>%s</p>' % d['delivery_text'], **body_font('16px', BROWN, weight='700', line='1.25em')),
            group('Servizi di consegna', [
                image(item['title'], media(item['src']), item['alt'], title=item['title'], link=item['url'], target='on',
                      width=R(vw(268, 160, 268), tablet='200px', phone='47.8%'), bottom_space=False)
                for item in d['delivery']
            ], layout(direction='row', gap=R(vw(70), phone='0px'), justify=R('center', phone='space-between'), align='center', wrap='wrap')),
        ], layout(direction='column', gap=R('30px', phone='20px'), align='stretch'),
            spacing(padding=box('48px', SIDE, '0px', R('50px', tablet=SIDE['tablet'], phone=SIDE['phone'])))),
    ]
    return section(
        'Menù', [
            row('Menù', [
                column(panel, layout(direction='column', gap='0px', align='stretch')),
            ], sizing(width=R('calc(100% - 56px)', phone='100%'), maxWidth='1456px'),
                # The panel is not in the middle of the screen: 19 px from the left on a 1512 px one, as in the Figma.
                spacing(padding=box(R('99px', tablet='56px', phone='0px'), '0px', R('48.6px', phone='72px'), '0px'),
                        margin=box('0px', 'auto', '0px', R('max(19px, calc(50% - 737px))', tablet='auto'))),
                background(color=CREAM), border(rad=R('20px', phone='0px'))),
        ],
        attrs(id='menu'),
        background(color=R(BORDEAUX, phone=CREAM)),
        spacing(padding=box(R('19px', phone='0px'), '0px', R('25px', phone='0px'), '0px')),
    )


# 0.8.3: a card is 402 x 517 (as wide as the page on phones): the bordeaux shape 284 x 227 at 50 px from the top with
# the photo 230 x 183 inside, the name in Bowlby One 24/24 35 px below it, the words in DM Sans 16/20.83.
CARD_WIDTH = '402px'


def ingredient_card(d, *attributes):
    return group('Scheda: ' + d['title'], [
        group('Foto con sfondo', [
            picture('Foto', d['image'], full=True, aspect=(230, 183), rad='999px'),
        ], background(color=BORDEAUX), border(rad='110px'), spacing(padding=box('6.04%', '7.42%')), sizing(width='78%', alignSelf='center')),
        # Two lines high even when the name takes one, so the words start at the same height in every card.
        heading('Titolo', d['title'], 'h3', spacing(margin=box(R('35px', phone='33px'), '0px', '-10px', '0px')), sizing(minHeight='58px'),
                **display_font('24px', BORDEAUX, line='1em', textAlign='center')),
        text('Testo', '<p>%s</p>' % d['text'], sizing(maxWidth=R('308px', phone='293px'), alignSelf='center'), spacing(margin=box('9px', '0px', '0px', '0px')),
             **body_font('16px', '#353535', line='1.302em', textAlign='center')),
    ], background(color=R('#f8ebdb', hover=GREEN)), border(rad='20px', width='3px', color=R(LINE, hover=GREEN)),
        spacing(padding=box(R('47px', phone='44.5px'), '16px', '30px', '16px')),
        layout(direction='column', gap='0px', align='stretch'),
        # On phones the card is 23 px wider than the texts of the page (4 px from the screen's sides at 390), as in the Figma.
        sizing(width=R(CARD_WIDTH, phone='calc(100% + 23px)'), maxWidth=R('100%', phone='none'), minHeight=R('517px', phone='491px')), *attributes)


# Where the cards stand on computers, as on the Figma's board (six places, then again from the first): how far from
# the left, as a share of the page's width, and how far from the card before (less than nothing: beside it).
CARD_PLACES = [
    ('15.9%', '0px'),
    ('min(64.46%, calc(100% - ' + CARD_WIDTH + '))', '-260px'),
    ('13.09%', '81px'),
    ('50.55%', '-23px'),
    ('7.64%', '-63px'),
    ('min(67.8%, calc(100% - ' + CARD_WIDTH + '))', '-349px'),
]


def ingredients():
    d = T.MATERIE
    cards = []
    for index, card in enumerate(d['cards']):
        left, above = CARD_PLACES[index % len(CARD_PLACES)]
        if index and index % len(CARD_PLACES) == 0:
            above = '81px'
        cards.append(ingredient_card(card, spacing(margin=box(R(above, tablet='0px'), '0px', '0px', R(left, tablet='0px', phone='-11.5px')))))
    pieces = []
    # The title and the words above the cards are there only if they are written (the site online has none).
    head = []
    if d.get('title'):
        head.append(title('Titolo', d['title'], 'h2', GREEN, capitalization='uppercase', upper_on_phone=False, line=R('1.0266em', phone='1em')))
    if d.get('html'):
        head.append(text('Testo', d['html'], spacing(margin=box(R('20.75px', phone='20px') if head else '0px', '0px', '0px', '0px')),
                         **body_font(COPY, GREEN, line=R('1.4em', phone='1.42em'))))
    if head:
        pieces.append(group('Titolo e testo', head, layout(direction='column', gap='0px', align='stretch'),
                            sizing(width=R('65.15%', tablet='100%')),
                            spacing(margin=box('0px', '0px', R('118px', tablet='56px', phone='20px'), R('8.61%', tablet='0px')))))
    pieces += [
        # tp-scorre: the words run. tp-resta: on computers they stay in view while the cards go by over them.
        # The two together: the line crosses the whole screen (its width and side margins are the theme's).
        # The line starts 368 px below the first card's top: the cards after it are pulled up by as much.
        text('Scritta obliqua che scorre', '<p>%s</p>' % d['marquee'], attrs(cls='tp-scorre tp-resta', data_durata='5'), hide_on(phone=True),
             transform(rotate='-4deg'), z_index('1'), spacing(margin=box(R('368px', tablet='0px'), '0px', '0px', '0px')),
             **display_font('72px', GREEN, line='1.3em')),
        # tp-carte: the cards come in one by one, a little turned, when they enter the screen.
        group('Schede', cards, attrs(cls='tp-carte'), z_index('2'),
              layout(direction='column', gap=R('0px', tablet='33px', phone='10px'), align=R('flex-start', tablet='center', phone='flex-start')),
              spacing(margin=box(R('-462px', tablet='40px', phone='0px'), '0px', '0px', '0px'))),
    ]
    return section(
        'Materie prime', [
            # On phones the page is already cream before this section: the wave is after the two cards above.
            wave('Onda', 'svg/onda-bordeaux-crema.svg', None, hide_on(phone=True)),
            row('Schede', [
                column(pieces, layout(direction='column', gap='0px', align='stretch')),
            ], WIDE, spacing(padding=box(R(vw(119) if head else '48px', tablet='48px', phone='0px'), '0px', '0px', '0px'))),
        ],
        attrs(id='materie'),
        background(color=CREAM),
        overflow('hidden', 'visible'),
        layout(direction='column', gap='0px', align='center'),
        spacing(padding=box(R('0px', phone='23px'), '0px', R('23px', tablet='60px', phone='125px'), '0px')),
    )


def location():
    # Figma (1512): the photo 573 x 594 at 137 px from the left; 71 px to its right the title (64/65.7), the words
    # (20/28), the address (20/28, bold, with the pin) and the button. On phones: title, words, address, photo, button.
    # A grid (Design > Layout): on computers the photo takes the first column and both lines, the words and the
    # button the second; on tablets and phones one column, in the order they are written here.
    d = T.LOCATION
    second = R('2', tablet='1')
    return section(
        'Dove siamo', [
            row('Dove siamo', [
                column([
                    group('Testi', [
                        title('Titolo', d['title'], 'h2', BORDEAUX, capitalization='uppercase', upper_on_phone=False,
                              size=R(vw(64, 40, 64), tablet='40px', phone='30px'), line=R('1.0266em', phone='1em')),
                        copy('Testo', '<p>%s</p>' % d['text'], BORDEAUX, spacing(margin=box(R('13px', phone='28px'), '0px', '0px', '0px')),
                             lineHeight=R('1.4em', phone='1.47em')),
                        group('Indirizzo', [
                            # The drawing has some air around the pin: a negative margin puts the pin itself on the texts' side.
                            image('Segnaposto', media('svg/location-map-pin.svg'), '', width='24px', bottom_space=False, shrink='0',
                                  margin=box('0px', '0px', '0px', R('-7px', phone='-4px'))),
                            text('Via', '<p>%s</p>' % d['address'], **body_font(COPY, BORDEAUX, weight='700', line='1.4em')),
                        ], layout(direction='row', gap=R('5px', phone='12px'), align='center', wrap='nowrap'),
                            spacing(margin=box(R('17px', phone='28px'), '0px', '0px', '0px'))),
                    ], layout(direction='column', gap='0px', align='stretch'),
                        sizing(gridColumnStart=second, gridRowStart='1'),
                        # The title is as wide as three of its lines ask: 30 px beyond the page's side, as in the Figma.
                        spacing(padding=box(R('50px', tablet='0px'), '0px', '0px', '0px'), margin=box('0px', R('-30px', tablet='0px'), '0px', '0px'))),
                    picture('Foto', d['image'], full=True, aspect=R((573, 594), tablet=(16, 10), phone=(356, 217)), rad='18px',
                            place={'gridColumnStart': '1', 'gridRowStart': R('1', tablet='2'), 'gridRowSpan': R('2', tablet='1')}),
                    cta('Pulsante', d['button'], d['button_url'], CREAM, BORDEAUX,
                        sizing(gridColumnStart=second, gridRowStart=R('2', tablet='3'), gridAlignSelf='start', gridJustifySelf='start'),
                        spacing(margin=box(R('40px', tablet='0px'), '0px', '0px', '0px')),
                        target='on', plain_on_phone=True, phone_side='50px'),
                ], at('module.decoration.layout', {
                    'display': 'grid', 'gridColumnWidths': 'manual', 'gridTemplateColumns': R('42.6% 1fr', tablet='1fr'),
                    'gridRowHeights': 'manual', 'gridTemplateRows': R('auto 1fr', tablet='auto auto auto'),
                    'columnGap': R('5.28%', tablet='0px'), 'rowGap': R('0px', tablet='28px'), 'alignItems': 'start'})),
            ], WIDE, spacing(padding=box('0px', '0px', '0px', R(vw(107, 0, 107), tablet='0px')))),
        ],
        attrs(id='location'),
        background(color=CREAM),
        spacing(padding=box(R('200px', tablet='80px', phone='0px'), '0px', R('204px', tablet='96px', phone='221px'), '0px')),
    )


def hours():
    # Figma (1512): boxes 690 px wide, the first with the title (Bowlby One 32) and the first day, then one box a
    # day, 126 px high, days and hours in DM Sans Black 20. They stand 47 px left of the middle, as in the Figma.
    # On phones: the title alone, then low cream boxes with the short names of the days, all in capitals.
    d = T.HOURS
    rows = [
        group('Titolo', [
            heading('Titolo', d['title'], 'h2', UNDER, **display_font('32px', R(BROWN, phone=BORDEAUX), line='1.002em', textAlign=R('center', phone='left'))),
        ], background(color=R('#f8ebdb', phone='transparent')),
            border(rad=radius('20px', '20px', R('0px', tablet='20px'), R('0px', tablet='20px')), width=R('3px', phone='0px'), color=LINE,
                   sides={'bottom': {'width': R('0px', tablet='3px', phone='0px'), 'color': LINE}}),
            spacing(padding=box(R('29px', tablet='34px', phone='0px'), R('30px', phone='0px'), R('26px', tablet='22px', phone='28px'), R('30px', phone='0px'))),
            layout(direction='column', align='stretch')),
    ]
    for index, item in enumerate(d['rows']):
        color = GREEN if index % 2 == 0 else BORDEAUX
        style = body_font(R('20px', phone='16.5px'), color, weight='900', line=R('1.002em', phone='1.25em'), capitalization=R('none', phone='uppercase'))
        # The row's space is inside the texts, more below than above, and a negative margin of the same amount takes
        # the extra off the row's height: in the Visual Builder Divi's "+" (some 30 px above the bottom of the piece
        # under the mouse) lands under the words and not on them.
        air = spacing(padding=box(R('49px', tablet='32px', phone='20px'), '0px', R('68px', tablet='50px', phone='40px'), '0px'),
                      margin=box('0px', '0px', R('-17px', tablet='-21px', phone='-19px'), '0px'))
        first = index == 0
        rows.append(group('Orario: ' + item['day'], [
            text('Giorno', '<p>%s</p>' % item['day'], air, sizing(flexShrink='0'), hide_on(phone=True), **style),
            text('Giorno (telefono)', '<p>%s</p>' % item.get('short', item['day']), air, sizing(flexShrink='0'), hide_on(desktop=True, tablet=True), **style),
            # A line without numbers ("Chiusi") stays as written; the others are in capitals on phones, as in the Figma.
            text('Orario', '<p>%s</p>' % item['hours'], air, sizing(flexGrow='1', minWidth='0px'),
                 **merge(style, {'textAlign': 'right'}, {} if any(ch.isdigit() for ch in item['hours']) else {'capitalization': 'none'})),
        ], background(color=R('#f8ebdb', phone=CREAM)),
            border(rad=radius(R('0px', tablet='20px', phone='10.3px'), R('0px', tablet='20px', phone='10.3px'), R('20px', phone='10.3px'), R('20px', phone='10.3px'))
                   if first else R('20px', phone='10.3px'),
                   width=R('3px', phone='1px'), color=LINE,
                   sides={'top': {'width': R('0px', tablet='3px', phone='1px'), 'color': LINE}} if first else None),
            spacing(padding=box(R('3px', tablet='0px') if first else '0px', R('38px', tablet='30px', phone='22px'), '0px', R('30px', phone='15px')),
                    margin=box('0px', '0px', R('0px', phone='1px'), '0px')),
            layout(direction='row', gap=R('24px', phone='12px'), justify='space-between', align='center', wrap='nowrap')))
    return section(
        'Orari', [
            row('Orari', [
                column(rows, layout(direction='column', gap='0px', align='stretch')),
            ], sizing(width=WIDTH_NARROW, maxWidth='690px'),
                spacing(margin=box('0px', 'auto', '0px', R('calc(50% - 392px)', tablet='auto')))),
        ],
        attrs(id='orari'),
        background(color=CREAM),
        spacing(padding=box('0px', '0px', R('0px', phone='146px'), '0px')),
    )


def faq():
    d = T.FAQ
    items = []
    for index, item in enumerate(d['items']):
        color = GREEN if index % 2 == 0 else BORDEAUX
        items.append(block(
            'divi/accordion-item', label('Domanda: ' + item['question']),
            at('title.innerContent', item['question']),
            at('content.innerContent', item['html']),
            at('module.advanced.open', 'on' if index == 0 else 'off'),
            at('title.decoration.font.font', body_font(R('20px', phone='17px'), color, weight='900', line='1.15em')),
            at('closedToggleIcon.decoration.icon', {'color': color, 'useSize': 'on', 'size': '20px'}),
            border(rad='20px', width='3px', color=LINE),
            spacing(padding=box(R('28px', phone='20px'), R('33px', phone='20px'))),
        ))
    accordion = block(
        'divi/accordion', label('Domande e risposte'),
        at('title.decoration.font.font', merge({'headingLevel': 'h3'}, body_font(R('20px', phone='17px'), GREEN, weight='900', line='1.15em'))),
        at('content.decoration.bodyFont.body.font', body_font(R('17px', phone='16px'), BROWN, line='1.5em')),
        at('closedToggle.decoration.background', {'color': PAPER}),
        at('openToggle.decoration.background', {'color': PAPER}),
        layout(direction='column', gap='10px'),
        children=items,
    )
    return section(
        'Informazioni utili', [
            row('Informazioni utili', [
                column([
                    group('Titolo', [
                        heading('Titolo', d['title'], 'h2', UNDER, **display_font(R('32px', phone='26px'), BROWN, line='1.02em', textAlign='center')),
                    ], background(color='#f8ebdb'), border(rad='20px', width='3px', color=LINE),
                        spacing(padding=box(R('34px', phone='24px'), R('33px', phone='20px'), R('22px', phone='16px'), R('33px', phone='20px'))),
                        layout(direction='column', align='stretch')),
                    accordion,
                ], layout(direction='column', gap='60px', align='stretch')),
            ], sizing(width=WIDTH_NARROW, maxWidth='690px')),
        ],
        attrs(id='faq'),
        background(color=CREAM),
        spacing(padding=box(R('96px', tablet='72px', phone='0px'), '0px', R('54px', phone='50px'), '0px')),
    )


def opening():
    """The first thing one sees: the logo on a full screen, then the page. On the site the theme turns this section
    into that screen (class tp-apertura); in the Visual Builder it is a section like the others, to change the
    logo or the colour. Without the class, or without the section, the page opens at once."""
    return section(
        'Apertura: il logo che si vede all\'inizio (solo sul sito)', [
            row('Logo', [
                column([
                    image('Logo', media('svg/logo-tasche-piene.svg'), 'Tasche Piene', width=R('clamp(220px, 31.95vw, 483px)'), align='center',
                          bottom_space=False),
                ], layout(direction='column', align='center')),
            ], WIDE),
        ],
        attrs(cls='tp-apertura'),
        background(color=CREAM),
        spacing(padding=box(R('60px', phone='40px'), '0px')),
    )


def gallery():
    """The photos at the end of the page: Divi's own Gallery module (Content > Images to add, remove or sort them)."""
    ids = ','.join('{{id:%s}}' % path for path in T.GALLERY['images'])
    return section(
        'Galleria', [
            row('Galleria', [
                column([
                    block('divi/gallery', label('Galleria di foto'),
                          at('image.advanced.galleryIds', ids),
                          at('image.advanced.orientation', 'portrait'),
                          at('module.advanced.fullwidth', 'off'),
                          # All the photos on one page (the number is "Images per page"): three in a row, two on phones.
                          at('module.advanced.postsNumber', '15'),
                          at('module.advanced.showTitleAndCaption', 'off'),
                          at('pagination.advanced.showPagination', 'off'),
                          at('galleryGrid.decoration.layout', {'display': 'grid', 'gridColumnCount': R('3', phone='2'),
                                                              'columnGap': '10px', 'rowGap': '10px'})),
                ]),
            ], sizing(width='90%', maxWidth='1296px')),
        ],
        attrs(id='galleria'),
        background(color='#315431'),
        spacing(padding=box('54px', '0px')),
    )


def home():
    sections = [opening(), hero(), strip(), intro(), alberto(), choice(), menu(), ingredients(), location(), hours(), faq()]
    if hasattr(T, 'GALLERY'):
        sections.append(gallery())
    return document(sections)


# ---------------------------------------------------------------------------------------------
# Header and footer (Theme Builder) and the legal pages.

def interaction(trigger, effect, target, target_label, trigger_id, **more):
    """Advanced > Interactions, as Divi stores one."""
    return merge({
        'id': uid('interaction:%s:%s:%s' % (trigger, effect, target)), 'enableInteraction': 'on', 'trigger': trigger, 'effect': effect,
        'target': {'targetClass': 'et-interaction-target-' + target, 'label': target_label, 'moduleId': '', 'targetType': 'module'},
        'replaceExistingPreset': False, 'sensitivity': 50, 'mouseMovementType': 'translate', 'cookieName': '', 'cookieValue': '',
        'triggerClass': 'et-interaction-trigger-' + trigger_id, 'presetId': '',
    }, more)


def header():
    # Figma (1512): a cream bar 76 px high, 14 px from the top and 50 px from the sides, that stays in sight: the
    # logo 216 px wide, the voices in Bowlby One 20 px, 63 px apart. On tablets and phones the bar is not there:
    # the cream logo and the sandwich lie over the hero and go up with the page; the sandwich opens the voices
    # in a cream box as wide as the screen.
    voices_id, burger_id = 'tpvocimenu', 'tppaninotp'
    voices_label = 'Gruppo (Voci del menù)'
    voices = group(
        'Voci del menù',
        # On computers a voice is only as high as its words: the bar's height comes from the row's padding. In the
        # Visual Builder Divi draws a "+" some 30 px above the bottom of the piece under the mouse: with a voice
        # this low it lands above the words and not on them, so a voice can be clicked and written.
        [link('Voce: ' + words, words, '{{url:home}}' + url, sizing(flexShrink='0'),
              spacing(padding=box(R('0px', tablet='16px'), R('0px', tablet='12px'), R('0px', tablet='17px'), R('0px', tablet='12px'))),
              border(sides={'top': {'width': R('0px', tablet='1px' if index else '0px'), 'color': LINE}}),
              **display_font(R(vw(20, 15, 20), tablet='18px'), R('#404040', hover=BORDEAUX), line='1em'))
         for index, (words, url) in enumerate(T.NAV)],
        # On tablets and phones the voices are hidden (Advanced > Visibility) until the sandwich is pressed.
        hide_on(tablet=True, phone=True),
        {'module': {'decoration': {'interactionTarget': voices_id}}},
        # The space between the voices grows with the screen: 16 px on a small laptop, 63 px from 1512 px up.
        # A voice never breaks in two lines (Sizing > Size of each voice): when there are more voices than the
        # bar can hold, the last ones go to a second line and the bar grows.
        layout(direction=R('row', tablet='column'), gap=R('clamp(14px, calc(9.5vw - 77px), 63px)', tablet='0px'), row_gap=R('18px', tablet='0px'),
               justify=R('flex-end', tablet='flex-start'), align=R('center', tablet='stretch'), wrap=R('wrap', tablet='nowrap')),
        background(color=R('transparent', tablet=CREAM)), border(rad=R('0px', tablet='15px')),
        # (As a least width too: a piece of a line gives way before it goes beyond its line's sides.)
        sizing(width=R('auto', tablet='calc(100% + 64px)', phone='calc(100% + 30px)'), maxWidth='none',
               minWidth=R('0px', tablet='calc(100% + 64px)', phone='calc(100% + 30px)')),
        spacing(padding=box(R('0px', tablet='12px')),
                margin=box(R('0px', tablet='6px'), '0px', '0px', R('0px', tablet='-32px', phone='-15px'))),
    )
    # Figma: three cream lines, 40 x 25. A picture, so its drawing can be changed like any other.
    burger = image(
        'Panino (tablet e telefono)', media('svg/menu-panino.svg'), 'Menù', hide_on(desktop=True),
        {'module': {'decoration': {'interactionTrigger': burger_id, 'interactions': rv({'interactions': [
            interaction('click', 'toggleVisibility', voices_id, voices_label, burger_id),
            # Back on a computer the voices are always there, whatever was pressed before.
            interaction('breakpointEnter', 'addVisibility', voices_id, voices_label, burger_id, breakpointName='desktop'),
            interaction('breakpointExit', 'removeVisibility', voices_id, voices_label, burger_id, breakpointName='desktop'),
        ]})}}},
        # aria-expanded: the theme's script keeps it true while the voices are open, and the theme's CSS then shows an X.
        attrs(cls='tp-panino', role='button', aria_label='Apri o chiudi il menù', aria_expanded='false', tabindex='0'),
        width='40px', margin=box('0px', '3px', '0px', '0px'), bottom_space=False, shrink='0',
    )
    return document([section(
        'Testata', [
            row('Barra', [
                column([
                    image('Logo (computer)', media('svg/logo-tasche-piene-testata.svg'), 'Tasche Piene', hide_on(tablet=True, phone=True), link='{{url:home}}',
                          width=vw(216, 150, 216), bottom_space=False, shrink='0'),
                    image('Logo chiaro (tablet e telefono)', media('svg/logo-tasche-piene-line-crema.svg'), 'Tasche Piene', hide_on(desktop=True),
                          link='{{url:home}}', width='168px', bottom_space=False, shrink='0'),
                    burger,
                    voices,
                ], layout(direction='row', gap=R('32px', tablet='0px'), justify='space-between', align='center', wrap=R('nowrap', tablet='wrap'))),
            ], INSET, background(color=R(CREAM, tablet='transparent')),
                # No round corners on tablets and phones: with them Divi cuts what goes beyond the bar, and the open
                # voices are as wide as the screen.
                border(rad=R('15px', tablet='0px')), overflow(R('hidden', tablet='visible'), R('hidden', tablet='visible')),
                spacing(padding=box(R('21.5px', tablet='14px'), R(vw(86, 32, 86), tablet='0px'), R('21.5px', tablet='14px'), R('32px', tablet='0px')))),
        ],
        # tp-fascia: on tablets and phones, on the pages that do not start with the video, the light logo and the
        # sandwich stand on a bordeaux band (assets/css/tp.css).
        attrs(cls='tp-testata tp-fascia'),
        position(R('fixed', tablet='absolute'), vertical='0px', horizontal='0px', origin='top left'), z_index('9999'), sizing(width='100%'),
        background(color='transparent'),
        spacing(padding=box(R('14px', tablet='33px', phone='53px'), '0px', '0px', '0px')),
    )])


def footer():
    # Figma (1512): a green block 823 px high. The name, as wide as the page, stands 478 px from its top with the
    # drawings sitting on the letters; below it the voices in Bowlby One 24 and a last line in DM Sans 16.
    # On phones: 110 px above the name, the voices in two lines (16 px), the notes one under the other (14 px).
    d = T.FOOTER
    nav_font = display_font(R(vw(23.8, 18, 24), tablet='20px', phone='16px'), R(CREAM, hover=WHITE), line='1em', capitalization='uppercase')
    small = body_font(R('16px', phone='14px'), R(WHITE, phone=CREAM), weight=R('300', phone='400'), line='1em')
    links = at('content.decoration.bodyFont.link.font', {'color': R(WHITE, phone=CREAM)})

    def voices(name, items):
        return group(name, [link('Voce: ' + item[0], item[0], item[1] if item[1].startswith('http') else '{{url:home}}' + item[1],
                                 target=(item[2] if len(item) > 2 else 'off'), **nav_font) for item in items],
                     layout(direction='row', gap=R('clamp(18px, 2.3vw, 34px)', phone='16px'), row_gap='14px', align='center', wrap='wrap'))

    return document([section(
        'Piè di pagina', [
            row('Piè di pagina', [
                column([
                    group('Logo e personaggi', [
                        image('Logo', media('images/logo-footer.webp'), 'Tasche Piene', link='{{url:home}}', full=True, bottom_space=False),
                        # The drawings sit on the letters: their place and size are parts of the logo's width, so they follow it.
                        image('Personaggi: la coppia', media('images/footer/personaggi-4.webp'), '', position('absolute', vertical='70%', horizontal='35.2%', origin='bottom left'),
                              z_index('2'), width='14.5%', bottom_space=False),
                        image('Personaggi: i gabbiani', media('images/footer/personaggi-1.webp'), '', position('absolute', vertical='84%', horizontal='81.4%', origin='bottom left'),
                              z_index('2'), width='9.8%', bottom_space=False),
                    ], layout(direction='column', align='stretch'),
                        # On phones the name is a little wider than the texts below it, as in the Figma.
                        sizing(width=R('100%', phone='calc(100% + 6px)'), maxWidth='none'),
                        spacing(margin=box('0px', '0px', '0px', R('0px', phone='-3px')))),
                    group('Voci', [voices('Voci a sinistra', d['left']), voices('Voci a destra', d['right'])],
                          layout(direction='row', gap='16px', row_gap='14px', justify='space-between', align='center', wrap='wrap')),
                    group('Note', [text('Nota %d' % (index + 1), '<p>%s</p>' % line, links, **small) for index, line in enumerate(d['lines'])],
                          layout(direction=R('row', phone='column'), gap=R('36px', phone='12px'), align=R('center', phone='flex-start'), wrap='wrap'),
                          spacing(padding=box(R('28px', phone='60px'), '0px', '0px', '0px'))),
                ], layout(direction='column', gap=R('22px', phone='12px'), align='stretch')),
            ], sizing(width=R('calc(100% - 72px)', phone='calc(100% - 32px)'), maxWidth='1500px')),
        ],
        background(color=GREEN),
        spacing(padding=box(R(vw(478, 200, 478), tablet='200px', phone='110px'), '0px', R('27px', phone='32px'), '0px')),
    )])


def legal(key):
    d = T.LEGAL[key]
    body = body_font(R('18px', phone='17px'), BROWN, weight='400', line='1.55em')
    return document([section(
        d['title'], [
            row(d['title'], [
                column([
                    heading('Titolo', d['title'], 'h1', **display_font(H2, BORDEAUX, line='1.05em')),
                    text('Testo', d['html'], at('content.decoration.bodyFont.link.font', {'color': BORDEAUX}),
                         headings={'h2': display_font(R('24px', phone='21px'), GREEN, line='1.2em')}, **body),
                    text('Collegamenti', d['back'], at('content.decoration.bodyFont.link.font', {'color': BORDEAUX}), **merge(body, {'weight': '700'})),
                ], layout(direction='column', gap=R('28px', phone='22px'), align='stretch')),
            ], sizing(width=WIDTH_NARROW, maxWidth='860px')),
        ],
        background(color=CREAM),
        spacing(padding=box(R('60px', phone='36px'), '0px', R('110px', phone='64px'), '0px')),
    )])


def write(name, content):
    # Every file a page asks for must be in assets/: a wrong name would only show when the site is created.
    missing = sorted({found for found in re.findall(r'\{\{(?:media|id):([^}]+)\}\}', content)
                      if not os.path.isfile(os.path.join(ROOT, 'assets', found))})
    assert not missing, '%s asks for files that are not in assets/: %s' % (name, ', '.join(missing))
    path = os.path.join(OUT, name)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf8', newline='\n') as handle:
        handle.write(content)
    print('%s: %d bytes, %d modules' % (name, len(content.encode('utf8')), content.count('<!-- wp:divi/')))


if __name__ == '__main__':
    write('home.html', home())
    write('header.html', header())
    write('footer.html', footer())
    write('privacy.html', legal('privacy'))
    write('cookie.html', legal('cookie'))
