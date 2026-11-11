#!/usr/bin/env python3
"""Writes the pages of the site as Divi 5 blocks: content/home.html, content/header.html, content/footer.html.

    python3 dev/build.py

The theme imports those files the first time (inc/setup-site.php). After that the pages live in
WordPress and are changed from Divi: this script is only the way they were first written.

{{media:path}} stands for a file of assets/: the import puts it in the Media Library and writes its address.
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from divi import *  # noqa: E402,F403
import texts as T  # noqa: E402

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

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

# The page is as wide as the screen less a margin on each side, up to 1432 px.
WIDE = sizing(width=R('calc(100% - 92px)', tablet='calc(100% - 64px)', phone='calc(100% - 32px)'), maxWidth='1432px')
FULL = sizing(width='100%', maxWidth='100%')
WIDTH_NARROW = R('calc(100% - 92px)', tablet='calc(100% - 64px)', phone='calc(100% - 32px)')
ARROW = {'unicode': '&#x26;', 'type': 'divi', 'weight': '400'}
# Divi leaves 10 px under every title: taken back where a title sits beside something else.
UNDER = spacing(margin=box('0px', '0px', '-10px', '0px'))


def media(path):
    return '{{media:%s}}' % path


def display_font(size, color, line='1.04em', **more):
    return dict(family=DISPLAY, weight='400', size=size, lineHeight=line, color=color, **more)


def body_font(size, color, weight='500', line='1.4em', **more):
    return dict(family=BODY, weight=weight, size=size, lineHeight=line, color=color, **more)


def cta(name, label_, url, ink, paper, *attributes, target='off'):
    """The button with the arrow: `ink` the text, `paper` the fill."""
    return button(
        name, label_, url,
        at('button.decoration.background', {'color': R(paper, hover=ink)}),
        border(rad='15px', width='2px', color=paper, element='button'),
        spacing(padding=box('12px', '26px')),
        at('module.advanced.alignment', 'left'),
        *attributes,
        target=target, icon=ARROW,
        family=BODY, weight='700', size=R('20px', phone='18px'), lineHeight='1.3em', color=R(ink, hover=paper),
    )


# ---------------------------------------------------------------------------------------------
# Pieces used more than once.

H2 = R('clamp(36px, 4.25vw, 64px)', phone='clamp(28px, 8.2vw, 34px)')        # a section's title
BIG = R('clamp(46px, 6.35vw, 96px)', phone='clamp(30px, 8.6vw, 40px)')       # the two largest titles
COPY = R('20px', tablet='19px', phone='18px')
SIDE = R('58px', tablet='32px', phone='18px')                                # the menu's inner side space


def title(name, words, level, color, *attributes, size=H2, upper_on_phone=True, **more):
    props = display_font(size, color, line=R('1.03em', phone='1.02em'), **more)
    if upper_on_phone:
        props['capitalization'] = R('none', phone='uppercase')
    return heading(name, words, level, *attributes, **props)


def copy(name, html, color, *attributes, **more):
    return text(name, html, *attributes, **merge(body_font(COPY, color), more))


def picture(name, item, *attributes, rad='20px', **options):
    """A photo: `item` has src (a file of assets/), alt and title. Options as in divi.image."""
    return image(name, media(item['src']), item.get('alt', ''), *attributes, title=item.get('title') or None, rad=rad, **options)


def rise(delay='0ms'):
    """Design > Animation: the piece comes up into place when it enters the screen."""
    return animation(style='slide', direction='bottom', duration='800ms', delay=delay, intensity='6%', curve='ease-out')


def wave(name, computer, phone):
    """A wave between two sections: one picture for computers and tablets, one for phones."""
    return row(name, [
        column([
            image(name + ' (computer e tablet)', media(computer), '', hide_on(phone=True), full=True, bottom_space=False),
            image(name + ' (telefono)', media(phone), '', hide_on(desktop=True, tablet=True), full=True, bottom_space=False),
        ], layout(direction='column', gap='0px')),
    ], FULL)


# ---------------------------------------------------------------------------------------------
# Home.

def hero():
    return section(
        'Hero', [
            row('Hero: testi', [
                column([
                    text('Titolo', '<h1>%s</h1>' % T.HERO_TITLE_HTML, attrs(cls='tp-lettere'),
                         headings={'h1': display_font(R('clamp(48px, 6.1vw, 96px)', tablet='clamp(40px, 7.4vw, 64px)', phone='clamp(30px, 8.6vw, 40px)'), CREAM,
                                                      line=R('1.04em', phone='1.18em'))}),
                    cta('Pulsante', T.HERO_BUTTON, '#menu', GREEN, CREAM),
                    text('Sottotitolo', '<p>%s</p>' % T.HERO_SUBLINE,
                         spacing(margin=box(R('34px', phone='70px'), '0px', '0px', '0px')),
                         **body_font(R('22px', phone='19px'), CREAM, line='1.15em')),
                ], layout(direction='column', gap=R('34px', phone='24px'), align='stretch')),
            ], WIDE),
        ],
        attrs(id='home', cls='tp-video-telefono'),
        background(color=INK, image={'url': media('images/hero-poster.webp'), 'size': 'cover', 'position': 'center', 'repeat': 'no-repeat'},
                   video={'mp4': media('video/videodefinitivo.mp4'), 'webm': media('video/videodefinitivo.webm'), 'width': '1920', 'height': '1080',
                          'allowPlayerPause': 'off', 'pauseOutsideViewport': 'on'}),
        sizing(minHeight=R('100vh', phone='96vh')),
        layout(direction='column', justify='flex-end', align='center'),
        spacing(padding=box(R('150px', phone='120px'), '0px', R('44px', phone='96px'), '0px')),
    )


def strip():
    return section(
        'Striscia che scorre', [
            row('Striscia', [
                column([
                    text('Scritta che scorre', '<p>%s</p>' % T.STRIP,
                         attrs(cls='tp-scorre'),
                         **display_font(R('22px', phone='13px'), CREAM, line='1.3em')),
                ]),
            ], FULL),
        ],
        background(color=GREEN),
        spacing(padding=box(R('24px', phone='9px'), '0px')),
    )


def intro():
    d = T.INTRO
    return section(
        'Introduzione', [
            row('Introduzione', [
                column([
                    picture('Foto', d['image'], transform(rotate=R('-5deg', tablet='0deg')), width='100%', max_width=R('480px', tablet='100%'),
                            aspect=R((480, 680), tablet=(4, 3)), rad=R('22px', phone='18px')),
                ], layout(direction='column', align=R('center', tablet='stretch')), size=R('9_24', tablet='24_24'), name='Colonna foto'),
                column([
                    title('Titolo', d['title'], 'h2', BORDEAUX),
                    copy('Testo', d['html'], BORDEAUX),
                    cta('Pulsante', d['button'], d['button_url'], CREAM, BORDEAUX),
                ], layout(direction='column', gap=R('28px', phone='22px'), align='stretch'), spacing(padding=box('0px', R('7%', tablet='0px'), '0px', '0px')),
                    size=R('15_24', tablet='24_24'), name='Colonna testi'),
            ], WIDE, layout(direction=R('row', tablet='column-reverse'), gap=R('9%', tablet='36px'), align=R('center', tablet='stretch'), wrap='nowrap'), columns=2),
        ],
        attrs(id='chi-siamo'),
        background(color=CREAM),
        spacing(padding=box(R('120px', tablet='72px', phone='56px'), '0px', R('90px', tablet='48px', phone='24px'), '0px')),
    )


def alberto():
    d = T.ALBERTO
    return section(
        'Alberto', [
            row('Alberto', [
                column([
                    title('Titolo', d['title'], 'h2', GREEN),
                    copy('Testo', d['html'], GREEN),
                ], layout(direction='column', gap=R('26px', phone='20px')), spacing(padding=box('0px', '0px', '0px', R('5%', tablet='0px'))),
                    size=R('15_24', tablet='24_24'), name='Colonna testi'),
                column([
                    picture('Foto in alto', d['photo_sub'], transform(rotate=R('7deg', phone='0deg')), z_index('3'),
                            width=R('62%', phone='100%'), aspect=R((348, 245), phone=(3, 2)),
                            margin=box('0px', '0px', R('-9%', phone='0px'), R('30%', phone='0px')), rad=R('16px', phone='18px')),
                    picture('Foto in basso', d['photo_main'], transform(rotate=R('-5deg', phone='0deg')),
                            width=R('92%', phone='100%'), aspect=R((522, 368), phone=(3, 2)), rad=R('16px', phone='18px')),
                ], layout(direction=R('column', phone='column-reverse'), gap=R('0px', phone='10px'), align=R('flex-start', phone='stretch')),
                    size=R('9_24', tablet='24_24'), name='Colonna foto'),
            ], WIDE, layout(direction=R('row', tablet='column'), gap=R('3%', tablet='32px'), align=R('center', tablet='stretch'), wrap='nowrap'), columns=2),
        ],
        background(color=CREAM),
        spacing(padding=box(R('60px', phone='40px'), '0px', R('70px', phone='40px'), '0px')),
    )


def chip_list(name, phrases, paper, icons):
    """The little labels of a card: an Icon List whose items are pills."""
    items = []
    for phrase, glyph in zip(phrases, icons):
        items.append(block(
            'divi/icon-list-item', label('Etichetta: ' + phrase),
            at('content.innerContent', phrase),
            at('icon.innerContent', {'unicode': glyph, 'type': 'fa', 'weight': '900'}),
            background(color=paper),
            border(rad='10px'),
            spacing(padding=box('2px', R('12px', phone='9px'))),
            layout(direction='row', gap='6px', align='center'),
        ))
    return block(
        'divi/icon-list', label(name),
        at('icon.advanced.color', CREAM), at('icon.advanced.size', R('14px', phone='11px')),
        at('listItem.decoration.font.font', display_font(R('clamp(11px, 1vw, 14px)', phone='10px'), CREAM, line='1.5em')),
        layout(direction='row', gap=R('8px', phone='6px'), wrap='wrap', align='center'),
        children=items,
    )


def choice_card(d, ink, icons, delay='0ms'):
    return column([
        picture('Foto', d['image'], full=True, aspect=R((600, 341), phone=(7, 4)), rad='12px'),
        title('Nome', d['name'], 'h3', ink, size=R('clamp(26px, 2.5vw, 38px)', phone='20px'), upper_on_phone=False),
        chip_list('Etichette', d['chips'], ink, icons),
        text('Descrizione', d['html'], **body_font(R('16px', phone='15px'), ink, weight='400', line='1.5em')),
    ], background(color=CREAM), border(rad='20px'), spacing(padding=box(R('30px', phone='17px'))),
        layout(direction='column', gap=R('18px', phone='12px')), rise(delay), size=R('12_24', tablet='24_24'), name='Scheda: ' + d['name'])


def choice():
    d = T.SCELTA
    return section(
        'Tasca o vaschetta', [
            wave('Onda', 'svg/onda-crema-bordeaux.svg', 'svg/onda-crema-bordeaux-telefono.svg'),
            row('Titolo', [
                column([
                    text('Titolo', '<h2>%s</h2>' % d['title_html'], attrs(cls='tp-lettere'),
                         headings={'h2': display_font(BIG, WHITE, line=R('1.1em', phone='1.02em'))}),
                ]),
            ], WIDE, spacing(margin=box(R('-11vw', tablet='-4vw', phone='0px'), 'auto', '0px', 'auto'))),
            row('Schede', [
                choice_card(d['cards'][0], GREEN, ['&#xf70e;', '&#xf2e7;', '&#xf805;']),
                choice_card(d['cards'][1], BORDEAUX, ['&#xf24e;', '&#xf4d8;', '&#xf7ec;'], delay='120ms'),
            ], WIDE, layout(direction=R('row', tablet='column'), gap=R('24px', phone='16px'), align='stretch', wrap='nowrap'), columns=2),
        ],
        attrs(id='come-mangi'),
        background(color=BORDEAUX),
        layout(direction='column', gap=R('56px', phone='28px'), align='center'),
        spacing(padding=box('0px', '0px', R('14px', phone='10px'), '0px')),
    )


# The menu. Each dish is a group one can duplicate: on computers a line of the table, on tablets and phones a card.
CELL_TASCA = 'clamp(118px, 11.46vw, 165px)'      # as wide as the green box of the legend
CELL_HALF = 'clamp(126px, 12.29vw, 177px)'       # as wide as half the bordeaux box
CELL_GAP = '8px'
PRICE = R('20px', tablet='19px', phone='18px')


# In the Visual Builder Divi draws its tools over the piece under the mouse: a small bar along the top
# (24 px) and a "+" in the middle, some 30 px above the bottom. A short text that is all there is in its
# box ends up under them and cannot be clicked. So a price keeps the row's space inside itself, a little
# more below than above: the bar sits over it, the "+" under it, the price stays free between the two.
AIR_ABOVE = R('30px', tablet='0px')
AIR_BELOW = R('46px', tablet='0px')


def price(name, value, color, *attributes):
    return text(name, '<p>%s</p>' % value, spacing(padding=box(AIR_ABOVE, '0px', AIR_BELOW, '0px')), *attributes,
                **body_font(PRICE, color, weight='900', line='1.1em', textAlign='center'))


def price_table(prices):
    """Tasca, media and grande: on computers three columns under the legend; on tablets and phones, where
    the legend is not shown, each price has its drawing and its name above. One grid for both: a row of
    drawings, a row of names (only tablets and phones), a row of prices."""
    kinds = [('tasca', 'Tasca', GREEN, 'svg/menu-tasca-icon.svg'), ('media', 'Media', BORDEAUX, 'svg/menu-piatto-m-icon.svg'),
             ('grande', 'Grande', BORDEAUX, 'svg/menu-piatto-l-icon.svg')]
    cells = [image('Disegno ' + kind, media(drawing), '', hide_on(desktop=True), width='42px', align='center', bottom_space=False)
             for kind, _, _, drawing in kinds]
    cells += [text('Nome ' + kind, '<p>%s</p>' % tag, hide_on(desktop=True), **body_font('14px', color, weight='600', line='1.1em', textAlign='center'))
              for kind, tag, color, _ in kinds]
    cells += [price('Prezzo ' + kind, value, color) for (kind, _, color, _), value in zip(kinds, prices)]
    return group('Prezzi', cells,
                 at('module.decoration.layout', {
                     'display': 'grid',
                     'gridColumnWidths': R('manual', tablet='equal'),
                     'gridTemplateColumns': '%s %s %s' % (CELL_TASCA, CELL_HALF, CELL_HALF),
                     'gridColumnCount': '3',
                     'columnGap': R(CELL_GAP, tablet='0px'),
                     'rowGap': R('0px', tablet='7px'),
                     'alignItems': 'center',
                 }),
                 sizing(flexShrink='0', width=R('auto', tablet='100%')))


def dish(d, table=True):
    """table=True: the three prices of tasca, media and grande; False: one price on the right."""
    name = [heading('Nome', d['name'], 'h3', UNDER, **body_font(R('20px', tablet='18px', phone='16px'), '#000000', weight='900', line='1.15em', capitalization='uppercase'))]
    if d.get('meta'):
        name.append(text('Descrizione', '<p>%s</p>' % d['meta'], attrs(cls='tp-al-passaggio'),
                         **body_font(R('13px', tablet='14px'), '#5b5348', weight='500', line='1.25em', capitalization=R('uppercase', tablet='none'))))
    # The name stands on the line of the prices (see AIR_ABOVE).
    parts = [group('Nome e descrizione', name, layout(direction='column', gap='6px', justify='flex-start'), sizing(flexGrow='1', flexShrink='1', minWidth='0px'),
                   spacing(padding=box(R('29px', tablet='0px'), '0px', R('22px', tablet='0px'), '0px')))]
    if d.get('image'):
        parts.append(image('Foto al passaggio del mouse', media(d['image']), '', attrs(cls='tp-al-passaggio'), hide_on(tablet=True, phone=True),
                           # Its right side stops before the prices, whatever the width of the screen.
                           position('absolute', horizontal='calc(%s + 2 * %s + 2 * %s + %s + 34px)' % (CELL_TASCA, CELL_HALF, CELL_GAP, SIDE['desktop']), origin='center right'),
                           transform(rotate='8deg'), z_index('20'),
                           shadow(vertical='18px', blur='40px', color='rgba(62,55,46,0.28)', element='image'),
                           width='clamp(210px, 21vw, 300px)', aspect=(3, 4), rad='10px', bottom_space=False))
    prices = d['prices']
    stacked = table and len(prices) == 3
    if stacked:
        parts.append(price_table(prices))
    elif table:
        # Only the tasca: its price stays in the tasca's column.
        parts.append(price('Prezzo tasca', prices[0], GREEN, sizing(width=R(CELL_TASCA, tablet='auto'), flexShrink='0'),
                           spacing(margin=box('0px', R('calc(2 * %s + 2 * %s)' % (CELL_HALF, CELL_GAP), tablet='0px'), '0px', '0px'))))
    else:
        parts.append(price('Prezzo', prices[0], d.get('color', GREEN), sizing(width=R(CELL_HALF, tablet='auto'), flexShrink='0')))
    return group(
        'Piatto: ' + d['name'], parts, attrs(cls='tp-piatto'),
        layout(direction=R('row', tablet='column') if stacked else 'row', gap=R('30px', tablet='22px') if stacked else R('30px', tablet='16px'),
               align=R('stretch', tablet='stretch') if stacked else R('stretch', tablet='center'), justify='space-between', wrap='nowrap'),
        spacing(padding=box(R('0px', tablet='22px'), R(SIDE['desktop'], tablet='20px'), R('0px', tablet='22px'), R('53px', tablet='20px'))),
        background(color=R(CREAM, hover=PAPER, tablet=PAPER)),
        border(rad=R('0px', tablet='18px'), sides={'bottom': {'width': '3px', 'color': LINE}, 'top': {'width': R('0px', tablet='3px'), 'color': LINE},
                                                  'left': {'width': R('0px', tablet='3px'), 'color': LINE}, 'right': {'width': R('0px', tablet='3px'), 'color': LINE}}),
    )


def dish_list(name, dishes, table=True, color=GREEN):
    rows = [dish(dict(d, color=color), table) for d in dishes]
    return group(name, rows, layout(direction='column', gap=R('0px', tablet='16px'), align='stretch'),
                 spacing(padding=box('0px', R('0px', tablet=SIDE['tablet'], phone=SIDE['phone']))),
                 border(sides={'top': {'width': R('3px', tablet='0px'), 'color': LINE}}))


def menu_head(d, color):
    return group('Titolo: ' + d['title'], [
        heading('Titolo', d['title'], 'h3', UNDER, **display_font(R('clamp(24px, 2.2vw, 32px)', phone='clamp(26px, 8vw, 34px)'), color, line='1.05em')),
        text('Sottotitolo', '<p>%s</p>' % d['subtitle'], **merge(display_font(R('clamp(24px, 2.2vw, 32px)', tablet='20px'), color, line='1.05em'),
                                                           {'family': R(DISPLAY, tablet=BODY), 'weight': R('400', tablet='700')})),
    ], layout(direction=R('row', tablet='column'), gap=R('28px', tablet='8px'), justify='space-between', align=R('center', tablet='flex-start'), wrap='wrap'),
        spacing(padding=box(R('96px', tablet='64px', phone='52px'), SIDE, R('38px', tablet='22px'), SIDE)))


def legend():
    d = T.MENU['legend']
    small = body_font('clamp(14px, 1.39vw, 20px)', CREAM, weight='900', line='1.05em')
    big = display_font('clamp(22px, 2.22vw, 32px)', CREAM, line='1em')
    return group('Legenda', [
        text('Legenda: tasca', '<h4>%s</h4><p>%s</p>' % (d[0]['name'], d[0]['lines'][0]),
             background(color=GREEN), border(rad='10px'), sizing(width=CELL_TASCA, flexShrink='0'), spacing(padding=box('24px', '6px', '20px', '6px')),
             headings={'h4': merge(big, {'textAlign': 'center'})}, **merge(small, {'textAlign': 'center'})),
        group('Legenda: vaschetta', [
            text('Nome', '<h4>%s</h4>' % d[1]['name'], spacing(padding=box('21px', '0px', '0px', '0px')), headings={'h4': big}),
            group('Misure', [
                text('Misura media', '<p>%s</p>' % d[1]['lines'][0], sizing(width='50%'), **small),
                text('Misura grande', '<p>%s</p>' % d[1]['lines'][1], sizing(width='50%'), **small),
            ], layout(direction='row', gap='0px', align='flex-start', wrap='nowrap')),
            image('Personaggio', media('svg/mascot-piatto.svg'), '', position('absolute', vertical='46%', horizontal='1.5%', origin='bottom right'),
                  z_index('3'), width='28%', bottom_space=False),
        ], background(color=BORDEAUX), border(rad='10px'), overflow('visible'), sizing(width='calc(2 * %s + %s)' % (CELL_HALF, CELL_GAP), flexShrink='0'),
            spacing(padding=box('0px', '14px', '20px', '20px')), layout(direction='column', gap='0px', align='stretch', justify='center')),
    ], hide_on(tablet=True, phone=True), layout(direction='row', gap=CELL_GAP, align='stretch', wrap='nowrap'), sizing(flexShrink='0'))


def menu():
    d = T.MENU
    panel = [
        group('Testata del menù', [
            heading('Titolo', d['title'], 'h2', attrs(cls='tp-lettere'), sizing(flexGrow='1', flexShrink='1', minWidth='0px'),
                    **display_font(BIG, R(BROWN, tablet=BORDEAUX), line='1.02em')),
            legend(),
        ], layout(direction='row', gap='32px', justify='space-between', align='flex-end', wrap=R('nowrap', tablet='wrap')),
            spacing(padding=box('0px', SIDE, R('50px', tablet='24px'), SIDE))),
        picture('Foto (tablet e telefono)', d['feature'][0], hide_on(desktop=True), align='center',
                width=R('100%', tablet='calc(100% - 64px)', phone='calc(100% - 36px)'), aspect=R((358, 322), tablet=(2, 1), phone=(358, 322)), rad='18px',
                margin=box('0px', 'auto', '20px', 'auto')),
        dish_list('Piatti', d['dishes']),
        menu_head(d['groups'][0], GREEN),
        dish_list('Contorni', d['groups'][0]['rows'], table=False, color=GREEN),
        menu_head(d['groups'][1], BORDEAUX),
        picture('Foto dei dolci (tablet e telefono)', d['feature'][1], hide_on(desktop=True), align='center',
                width=R('100%', tablet='calc(100% - 64px)', phone='calc(100% - 36px)'), aspect=R((358, 322), tablet=(2, 1), phone=(358, 322)), rad='18px',
                margin=box('0px', 'auto', '20px', 'auto')),
        dish_list('Dolci', d['groups'][1]['rows'], table=False, color=BORDEAUX),
        menu_head(d['groups'][2], GREEN),
        dish_list('Da bere', d['groups'][2]['rows'], table=False, color=GREEN),
        text('Allergeni e note', d['note'], sizing(maxWidth='600px'), spacing(padding=box(R('96px', tablet='56px'), SIDE, '0px', SIDE)),
             **body_font(R('16px', phone='14px'), BROWN, weight='400', line='1.2em')),
        group('Consegna a domicilio', [
            text('Testo', '<p>%s</p>' % d['delivery_text'], **body_font(R('16px', phone='15px'), BROWN, weight='700', line='1.2em')),
            group('Servizi di consegna', [
                image(item['title'], media(item['src']), item['alt'], title=item['title'], link=item['url'], target='on',
                      width=R('clamp(160px, 18.6vw, 268px)', phone='42%'), bottom_space=False)
                for item in d['delivery']
            ], layout(direction='row', gap=R('60px', phone='12px'), justify='center', align='center', wrap='wrap')),
        ], layout(direction='column', gap=R('30px', phone='18px'), align='stretch'), spacing(padding=box('30px', SIDE, '0px', SIDE))),
    ]
    return section(
        'Menù', [
            row('Menù', [
                column(panel, layout(direction='column', gap='0px', align='stretch')),
            ], sizing(width=R('calc(100% - 56px)', phone='calc(100% - 24px)'), maxWidth='1456px'),
                background(color=CREAM), border(rad='20px'),
                spacing(padding=box(R('96px', tablet='56px', phone='36px'), '0px', R('48px', phone='32px'), '0px'))),
        ],
        attrs(id='menu'),
        background(color=BORDEAUX),
        spacing(padding=box(R('18px', phone='10px'), '0px', R('26px', phone='14px'), '0px')),
    )


def ingredient_card(d, *attributes):
    return group('Scheda: ' + d['title'], [
        group('Foto con sfondo', [
            picture('Foto', d['image'], full=True, aspect=(5, 4), rad='999px'),
        ], background(color=BORDEAUX), border(rad='110px'), spacing(padding=box('8%', '10%')), sizing(width='72%', alignSelf='center')),
        heading('Titolo', d['title'], 'h3', **display_font(R('clamp(19px, 1.67vw, 24px)', tablet='24px', phone='22px'), BORDEAUX, line='1em', textAlign='center')),
        text('Testo', '<p>%s</p>' % d['text'], sizing(maxWidth='300px', alignSelf='center'),
             **body_font(R('16px', phone='15px'), '#353535', line='1.3em', textAlign='center')),
    ], background(color=PAPER), border(rad='20px', width='3px', color=LINE),
        spacing(padding=box(R('44px', phone='28px'), '18px', R('40px', phone='30px'), '18px')),
        layout(direction='column', gap='10px', align='stretch'),
        sizing(width=R('clamp(280px, 27vw, 383px)', tablet='100%')), *attributes)


def ingredients():
    d = T.MATERIE
    cards = d['cards']
    # On computers the cards are out of line, as on the board: each one has its own left and top margins.
    stagger = [
        box('0px', '0px', '0px', R('17.5%', tablet='0px')),
        box(R('-17vw', tablet='0px'), '0px', '0px', R('65%', tablet='0px')),
        box(R('5vw', tablet='0px'), '0px', '0px', R('15%', tablet='0px')),
        box(R('9vw', tablet='0px'), '0px', '0px', R('10%', tablet='0px')),
    ]
    return section(
        'Materie prime', [
            wave('Onda', 'svg/onda-bordeaux-crema.svg', 'svg/onda-bordeaux-crema-telefono.svg'),
            row('Titolo e testo', [
                column([
                    title('Titolo', d['title'], 'h2', GREEN, capitalization='uppercase', upper_on_phone=False),
                    copy('Testo', d['html'], GREEN),
                ], layout(direction='column', gap=R('20px', phone='18px')), sizing(maxWidth='900px')),
            ], WIDE, spacing(padding=box(R('70px', phone='20px'), '0px', '0px', R('7%', tablet='0px')))),
            row('Schede', [
                column([
                    text('Scritta obliqua che scorre', '<p>%s</p>' % d['marquee'], attrs(cls='tp-scorre'), hide_on(phone=True),
                         transform(rotate='-4deg'), z_index('1'), spacing(margin=box(R('17vw', tablet='0px'), '-4%', '0px', '-4%')),
                         **display_font(R('clamp(44px, 5vw, 72px)', tablet='40px'), GREEN, line='1.3em')),
                    group('Schede', [ingredient_card(card, spacing(margin=margin), rise('%dms' % (index * 110)))
                                     for index, (card, margin) in enumerate(zip(cards, stagger))],
                          z_index('2'), layout(direction='column', gap=R('0px', tablet='20px'), align=R('flex-start', tablet='stretch')),
                          spacing(margin=box(R('-23vw', tablet='28px'), '0px', '0px', '0px'))),
                ], layout(direction='column', gap='0px', align='stretch')),
            ], WIDE),
        ],
        attrs(id='materie'),
        background(color=CREAM),
        overflow('hidden', 'visible'),
        layout(direction='column', gap=R('40px', phone='28px'), align='center'),
        spacing(padding=box('0px', '0px', R('120px', phone='56px'), '0px')),
    )


def location():
    d = T.LOCATION
    return section(
        'Dove siamo', [
            row('Dove siamo', [
                column([
                    picture('Foto', d['image'], full=True, aspect=R((546, 566), tablet=(16, 10), phone=(357, 217)), rad=R('20px', phone='18px')),
                ], size=R('10_24', tablet='24_24'), name='Colonna foto'),
                column([
                    title('Titolo', d['title'], 'h2', BORDEAUX, capitalization='uppercase', upper_on_phone=False),
                    copy('Testo', '<p>%s</p>' % d['text'], BORDEAUX),
                    group('Indirizzo', [
                        image('Segnaposto', media('svg/location-map-pin.svg'), '', width='24px', bottom_space=False),
                        text('Via', '<p>%s</p>' % d['address'], **body_font(COPY, BORDEAUX, weight='700', line='1.3em')),
                    ], layout(direction='row', gap='10px', align='center', wrap='nowrap')),
                    cta('Pulsante', d['button'], d['button_url'], CREAM, BORDEAUX, target='on'),
                ], layout(direction='column', gap=R('22px', phone='18px'), align='stretch'), size=R('14_24', tablet='24_24'), name='Colonna testi'),
            ], WIDE, layout(direction=R('row', tablet='column-reverse'), gap=R('5%', tablet='28px'), align=R('center', tablet='stretch'), wrap='nowrap'),
                spacing(padding=box('0px', R('4.5%', tablet='0px'))), columns=2),
        ],
        attrs(id='location'),
        background(color=CREAM),
        spacing(padding=box(R('60px', phone='24px'), '0px', R('90px', phone='48px'), '0px')),
    )


def hours():
    d = T.HOURS
    boxed = [background(color=PAPER), border(rad='14px', width='3px', color=LINE)]
    rows = [
        group('Titolo', [
            heading('Titolo', d['title'], 'h2', UNDER, **display_font(R('clamp(24px, 2.2vw, 32px)', phone='28px'), R(BROWN, phone=BORDEAUX), line='1.05em', textAlign=R('center', phone='left'))),
        ], *boxed, spacing(padding=box(R('26px', phone='20px'), R('30px', phone='16px'))), layout(direction='column', align='stretch')),
    ]
    for index, item in enumerate(d['rows']):
        color = GREEN if index % 2 == 0 else BORDEAUX
        style = body_font(R('20px', phone='17px'), color, weight='900', line='1.2em')
        # The row's space is inside the two texts, more below than above, and a negative margin of the same
        # amount takes the extra off the row's height: the row is as high as before, and in the Visual Builder
        # Divi's "+" (some 30 px above the bottom of the piece under the mouse) lands under the words.
        air = spacing(padding=box(R('28px', phone='20px'), '0px', R('44px', phone='42px'), '0px'),
                      margin=box('0px', '0px', R('-16px', phone='-22px'), '0px'))
        rows.append(group('Orario: ' + item['day'], [
            text('Giorno', '<p>%s</p>' % item['day'], air, sizing(flexShrink='0'), **style),
            text('Orario', '<p>%s</p>' % item['hours'], air, sizing(flexGrow='1', minWidth='0px'), **merge(style, {'textAlign': 'right'})),
        ], *boxed, spacing(padding=box('0px', R('30px', phone='16px')), margin=box('-3px', '0px', '0px', '0px')),
            layout(direction='row', gap='16px', justify='space-between', align='center', wrap='nowrap')))
    return section(
        'Orari', [
            row('Orari', [
                column(rows, layout(direction='column', gap='0px', align='stretch')),
            ], sizing(width=WIDTH_NARROW, maxWidth='660px')),
        ],
        attrs(id='orari'),
        background(color=CREAM),
        spacing(padding=box(R('30px', phone='16px'), '0px', R('70px', phone='40px'), '0px')),
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
            at('title.decoration.font.font', body_font(R('20px', phone='17px'), color, weight='900', line='1.2em')),
            at('closedToggleIcon.decoration.icon', {'color': color, 'useSize': 'on', 'size': '20px'}),
            border(rad='20px', width='3px', color=LINE),
            spacing(padding=box(R('26px', phone='20px'), R('32px', phone='20px'))),
        ))
    accordion = block(
        'divi/accordion', label('Domande e risposte'),
        at('title.decoration.font.font', merge({'headingLevel': 'h3'}, body_font(R('20px', phone='17px'), GREEN, weight='900', line='1.2em'))),
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
                        heading('Titolo', d['title'], 'h2', UNDER, **display_font(R('clamp(24px, 2.2vw, 32px)', phone='26px'), BROWN, line='1.05em', textAlign='center')),
                    ], background(color=PAPER), border(rad='20px', width='3px', color=LINE), spacing(padding=box(R('34px', phone='22px'), '20px')),
                        layout(direction='column', align='stretch')),
                    accordion,
                ], layout(direction='column', gap=R('44px', phone='28px'), align='stretch')),
            ], sizing(width=WIDTH_NARROW, maxWidth='690px')),
        ],
        attrs(id='faq'),
        background(color=CREAM),
        spacing(padding=box(R('40px', phone='20px'), '0px', R('120px', phone='64px'), '0px')),
    )


def home():
    return document([hero(), strip(), intro(), alberto(), choice(), menu(), ingredients(), location(), hours(), faq()])


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
    voices_id, burger_id = 'tpvocimenu', 'tppaninotp'
    voices_label = 'Gruppo (Voci del menù)'
    voices = group(
        'Voci del menù',
        # On computers a voice is only as high as its words: the bar's height comes from the row's padding. In the
        # Visual Builder Divi draws a "+" some 30 px above the bottom of the piece under the mouse: with a voice
        # this low it lands above the words and not on them, so a voice can be clicked and written.
        [link('Voce: ' + words, words, '{{url:home}}' + url, sizing(flexShrink='0'), spacing(padding=box(R('0px', tablet='4px'), '0px')),
              **display_font(R('clamp(15px, 1.39vw, 20px)', tablet='22px'), R(BROWN, hover=BORDEAUX), line='1.2em'))
         for words, url in T.NAV],
        # On tablets and phones the voices are hidden (Advanced > Visibility) until the sandwich is pressed.
        hide_on(tablet=True, phone=True),
        {'module': {'decoration': {'interactionTarget': voices_id}}},
        # The space between the voices grows with the screen: 16 px on a small laptop, 60 px from 1440 px up.
        # A voice never breaks in two lines (Sizing > Size of each voice): when there are more voices than the
        # bar can hold, the last ones go to a second line and the bar grows.
        layout(direction=R('row', tablet='column'), gap=R('clamp(14px, calc(9.5vw - 77px), 60px)', tablet='20px'), row_gap=R('18px', tablet='20px'),
               justify=R('flex-end', tablet='flex-start'), align=R('center', tablet='flex-start'), wrap=R('wrap', tablet='nowrap')),
        sizing(width=R('auto', tablet='100%')),
        spacing(padding=box(R('0px', tablet='14px'), '0px', R('0px', tablet='10px'), '0px'), margin=box(R('0px', tablet='14px'), '0px', '0px', '0px')),
        border(sides={'top': {'width': R('0px', tablet='2px'), 'color': LINE}}),
    )
    burger = icon(
        'Panino (tablet e telefono)', '&#x61;', hide_on(desktop=True),
        {'module': {'decoration': {'interactionTrigger': burger_id, 'interactions': rv({'interactions': [
            interaction('click', 'toggleVisibility', voices_id, voices_label, burger_id),
            # Back on a computer the voices are always there, whatever was pressed before.
            interaction('breakpointEnter', 'addVisibility', voices_id, voices_label, burger_id, breakpointName='desktop'),
            interaction('breakpointExit', 'removeVisibility', voices_id, voices_label, burger_id, breakpointName='desktop'),
        ]})}}},
        attrs(cls='tp-panino', role='button', aria_label='Apri o chiudi il menù', tabindex='0'),
        color=BORDEAUX, size='34px',
    )
    return document([section(
        'Testata', [
            row('Barra', [
                column([
                    image('Logo', media('svg/logo-tasche-piene-line.svg'), 'Tasche Piene', link='{{url:home}}',
                          width=R('clamp(150px, 14.3vw, 206px)', tablet='168px'), bottom_space=False, shrink='0'),
                    burger,
                    voices,
                ], layout(direction='row', gap=R('32px', tablet='0px'), justify=R('space-between', tablet='space-between'), align='center', wrap=R('nowrap', tablet='wrap'))),
            ], sizing(width=R('calc(100% - 100px)', tablet='calc(100% - 32px)', phone='calc(100% - 24px)'), maxWidth='1432px'),
                background(color=CREAM), border(rad='15px'),
                spacing(padding=box(R('19px', tablet='14px'), R('clamp(24px, 3.6vw, 52px)', tablet='20px')))),
        ],
        attrs(cls='tp-testata'),
        position('fixed', vertical='0px', horizontal='0px', origin='top left'), z_index('9999'), sizing(width='100%'),
        background(color='transparent'),
        spacing(padding=box(R('12px', phone='10px'), '0px', '0px', '0px')),
    )])


def footer():
    d = T.FOOTER
    nav_font = display_font(R('clamp(18px, 1.67vw, 24px)', phone='17px'), R(CREAM, hover=WHITE), line='1.2em', capitalization='uppercase')
    small = body_font(R('16px', phone='14px'), CREAM, weight='400', line='1.3em')
    links = at('content.decoration.bodyFont.link.font', {'color': CREAM})

    def voices(name, items):
        return group(name, [link('Voce: ' + item[0], item[0], item[1] if item[1].startswith('http') else '{{url:home}}' + item[1],
                                 target=(item[2] if len(item) > 2 else 'off'), **nav_font) for item in items],
                     layout(direction='row', gap=R('clamp(18px, 2.3vw, 34px)', phone='16px'), align='center', wrap='wrap'))

    return document([section(
        'Piè di pagina', [
            row('Piè di pagina', [
                column([
                    group('Logo e personaggi', [
                        image('Logo', media('images/logo-footer.webp'), 'Tasche Piene', link='{{url:home}}', full=True, bottom_space=False),
                        # The drawings sit on the letters: their place and size are parts of the logo's width, so they follow it.
                        image('Personaggi: la coppia', media('images/footer/personaggi-4.webp'), '', position('absolute', vertical='70%', horizontal='35.2%', origin='bottom left'),
                              z_index('2'), width='13.5%', bottom_space=False),
                        image('Personaggi: i gabbiani', media('images/footer/personaggi-1.webp'), '', position('absolute', vertical='84%', horizontal='81.4%', origin='bottom left'),
                              z_index('2'), width='9.8%', bottom_space=False),
                    ], layout(direction='column', align='stretch'), spacing(margin=box(R('13vw', phone='16vw'), '0px', '0px', '0px'))),
                    group('Voci', [voices('Voci a sinistra', d['left']), voices('Voci a destra', d['right'])],
                          layout(direction='row', gap='16px', justify='space-between', align='center', wrap='wrap')),
                    group('Note', [text('Nota %d' % (index + 1), '<p>%s</p>' % line, links, **small) for index, line in enumerate(d['lines'])],
                          layout(direction=R('row', phone='column'), gap=R('36px', phone='8px'), align=R('center', phone='flex-start'), wrap='wrap'),
                          spacing(padding=box(R('22px', phone='26px'), '0px', '0px', '0px'))),
                ], layout(direction='column', gap=R('18px', phone='10px'), align='stretch')),
            ], sizing(width=R('calc(100% - 72px)', phone='calc(100% - 32px)'), maxWidth='1500px')),
        ],
        attrs(cls='tp-pie'),
        background(color=GREEN),
        spacing(padding=box(R('60px', phone='40px'), '0px', R('34px', phone='28px'), '0px')),
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
    path = os.path.join(ROOT, 'content', name)
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
