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
ARROW = {'unicode': '&#x26;', 'type': 'divi', 'weight': '400'}


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
                ], layout(direction='column', gap=R('34px', phone='24px'), align='flex-start')),
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
                    picture('Foto', d['image'], transform(rotate=R('-5deg', tablet='0deg')), max_width=R('480px', tablet='100%'), full=True,
                            aspect=R((480, 680), tablet=(4, 3)), rad=R('22px', phone='18px')),
                ], layout(direction='column', align=R('center', tablet='stretch')), size=R('9_24', tablet='24_24'), name='Colonna foto'),
                column([
                    title('Titolo', d['title'], 'h2', BORDEAUX),
                    copy('Testo', d['html'], BORDEAUX),
                    cta('Pulsante', d['button'], d['button_url'], CREAM, BORDEAUX),
                ], layout(direction='column', gap=R('28px', phone='22px'), align='flex-start'), size=R('15_24', tablet='24_24'), name='Colonna testi'),
            ], WIDE, layout(direction=R('row', tablet='column-reverse'), gap=R('8%', tablet='36px'), align=R('center', tablet='stretch'), wrap='nowrap'), columns=2),
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
                    size=R('14_24', tablet='24_24'), name='Colonna testi'),
                column([
                    picture('Foto in alto', d['photo_sub'], transform(rotate=R('7deg', phone='0deg')), z_index('3'),
                            width=R('62%', phone='100%'), aspect=R((348, 245), phone=(3, 2)),
                            margin=box('0px', '0px', R('-9%', phone='0px'), R('30%', phone='0px')), rad=R('16px', phone='18px')),
                    picture('Foto in basso', d['photo_main'], transform(rotate=R('-5deg', phone='0deg')),
                            width=R('92%', phone='100%'), aspect=R((522, 368), phone=(3, 2)), rad=R('16px', phone='18px')),
                ], layout(direction=R('column', phone='column-reverse'), gap=R('0px', phone='10px'), align=R('flex-start', phone='stretch')),
                    size=R('10_24', tablet='24_24'), name='Colonna foto'),
            ], WIDE, layout(direction=R('row', tablet='column'), gap=R('5%', tablet='32px'), align=R('center', tablet='stretch'), wrap='nowrap'), columns=2),
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


def choice_card(d, ink, icons):
    return column([
        picture('Foto', d['image'], full=True, aspect=R((600, 341), phone=(7, 4)), rad='12px'),
        title('Nome', d['name'], 'h3', ink, size=R('clamp(26px, 2.5vw, 38px)', phone='20px'), upper_on_phone=False),
        chip_list('Etichette', d['chips'], ink, icons),
        text('Descrizione', d['html'], **body_font(R('16px', phone='15px'), ink, weight='400', line='1.5em')),
    ], background(color=CREAM), border(rad='20px'), spacing(padding=box(R('30px', phone='17px'))),
        layout(direction='column', gap=R('18px', phone='12px')), size=R('12_24', tablet='24_24'), name='Scheda: ' + d['name'])


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
            ], WIDE, spacing(padding=box(R('10px', phone='0px'), '0px', '0px', '0px'))),
            row('Schede', [
                choice_card(d['cards'][0], GREEN, ['&#xf70e;', '&#xf2e7;', '&#xf805;']),
                choice_card(d['cards'][1], BORDEAUX, ['&#xf24e;', '&#xf4d8;', '&#xf7ec;']),
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


def price_cell(name, value, color, width, drawing=None, tag=None, *attributes):
    """A price; on tablets and phones, where the legend is not shown, with its drawing and its name."""
    children = []
    if drawing:
        children.append(image(name + ': disegno', media(drawing), '', hide_on(desktop=True), width='42px', align='center', bottom_space=False))
    if tag:
        children.append(text(name + ': nome', '<p>%s</p>' % tag, hide_on(desktop=True), **body_font('14px', color, weight='600', line='1.1em', textAlign='center')))
    children.append(text(name, '<p>%s</p>' % value, **body_font(PRICE, color, weight='900', line='1.1em', textAlign='center')))
    return group(name, children, sizing(width=width, flexShrink='0'), layout(direction='column', gap='7px', align='center'), *attributes)


def dish(d, table=True):
    """table=True: the three prices of tasca, media and grande; False: one price on the right."""
    name = [heading('Nome', d['name'], 'h3', **body_font(R('20px', tablet='18px', phone='16px'), '#000000', weight='900', line='1.15em', capitalization='uppercase'))]
    if d.get('meta'):
        name.append(text('Descrizione', '<p>%s</p>' % d['meta'], attrs(cls='tp-al-passaggio'),
                         **body_font(R('13px', tablet='14px'), '#5b5348', weight='500', line='1.25em', capitalization=R('uppercase', tablet='none'))))
    parts = [group('Nome e descrizione', name, layout(direction='column', gap='6px'), sizing(flexGrow='1', flexShrink='1', minWidth='0px'))]
    if d.get('image'):
        parts.append(image('Foto al passaggio del mouse', media(d['image']), '', attrs(cls='tp-al-passaggio'), hide_on(tablet=True, phone=True),
                           position('absolute', horizontal='36%', origin='center left'), transform(rotate='8deg'), z_index('20'),
                           shadow(vertical='18px', blur='40px', color='rgba(62,55,46,0.28)', element='image'),
                           width='clamp(210px, 21vw, 300px)', aspect=(3, 4), rad='10px', bottom_space=False))
    prices = d['prices']
    if table and len(prices) == 3:
        cells = [
            price_cell('Prezzo tasca', prices[0], GREEN, R(CELL_TASCA, tablet='33.33%'), 'svg/menu-tasca-icon.svg', 'Tasca'),
            price_cell('Prezzo media', prices[1], BORDEAUX, R(CELL_HALF, tablet='33.33%'), 'svg/menu-piatto-m-icon.svg', 'Media'),
            price_cell('Prezzo grande', prices[2], BORDEAUX, R(CELL_HALF, tablet='33.33%'), 'svg/menu-piatto-l-icon.svg', 'Grande'),
        ]
        stacked = True
    elif table:
        # Only the tasca: its price stays in the tasca's column.
        cells = [price_cell('Prezzo tasca', prices[0], GREEN, R(CELL_TASCA, tablet='auto'), None, None,
                            spacing(margin=box('0px', R('calc(2 * %s + 2 * %s)' % (CELL_HALF, CELL_GAP), tablet='0px'), '0px', '0px')))]
        stacked = False
    else:
        cells = [price_cell('Prezzo', prices[0], d.get('color', GREEN), R(CELL_HALF, tablet='auto'))]
        stacked = False
    parts.append(group('Prezzi', cells,
                       layout(direction='row', gap=R(CELL_GAP, tablet='0px'), align=R('center', tablet='flex-end'), justify='flex-start', wrap='nowrap'),
                       sizing(flexShrink='0', width=R('auto', tablet='100%') if stacked else 'auto')))
    return group(
        'Piatto: ' + d['name'], parts, attrs(cls='tp-piatto'),
        layout(direction=R('row', tablet='column') if stacked else 'row', gap=R('30px', tablet='22px') if stacked else R('30px', tablet='16px'),
               align=R('center', tablet='stretch') if stacked else 'center', justify='space-between', wrap='nowrap'),
        spacing(padding=box(R('26px', tablet='22px'), R(SIDE['desktop'], tablet='20px'), R('26px', tablet='22px'), R('53px', tablet='20px'))),
        sizing(minHeight=R('100px', tablet='0px')),
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
        heading('Titolo', d['title'], 'h3', **display_font(R('clamp(24px, 2.2vw, 32px)', phone='clamp(26px, 8vw, 34px)'), color, line='1.05em')),
        text('Sottotitolo', '<p>%s</p>' % d['subtitle'], **merge(display_font(R('clamp(24px, 2.2vw, 32px)', tablet='20px'), color, line='1.05em'),
                                                           {'family': R(DISPLAY, tablet=BODY), 'weight': R('400', tablet='700')})),
    ], layout(direction=R('row', tablet='column'), gap=R('28px', tablet='8px'), justify='space-between', align=R('flex-end', tablet='flex-start'), wrap='wrap'),
        spacing(padding=box(R('96px', tablet='64px', phone='52px'), SIDE, R('38px', tablet='22px'), SIDE)))


def legend():
    d = T.MENU['legend']
    small = body_font('clamp(14px, 1.39vw, 20px)', CREAM, weight='900', line='1.05em')
    big = display_font('clamp(22px, 2.22vw, 32px)', CREAM, line='1em')
    return group('Legenda', [
        group('Legenda: tasca', [
            text('Nome', '<p>%s</p>' % d[0]['name'], **merge(big, {'textAlign': 'center'})),
            text('Dettaglio', '<p>%s</p>' % d[0]['lines'][0], **merge(small, {'textAlign': 'center'})),
        ], background(color=GREEN), border(rad='10px'), sizing(width=CELL_TASCA, flexShrink='0'), spacing(padding=box('22px', '6px', '20px', '6px')),
            layout(direction='column', gap='6px', align='stretch', justify='center')),
        group('Legenda: vaschetta', [
            text('Nome', '<p>%s</p>' % d[1]['name'], **big),
            group('Misure', [
                text('Misura media', '<p>%s</p>' % d[1]['lines'][0], sizing(width='50%'), **small),
                text('Misura grande', '<p>%s</p>' % d[1]['lines'][1], sizing(width='50%'), **small),
            ], layout(direction='row', gap='0px', align='flex-start', wrap='nowrap')),
            image('Personaggio', media('svg/mascot-piatto.svg'), '', position('absolute', vertical='-62%', horizontal='4%', origin='top right'),
                  z_index('3'), width='28%', bottom_space=False),
        ], background(color=BORDEAUX), border(rad='10px'), sizing(width='calc(2 * %s + %s)' % (CELL_HALF, CELL_GAP), flexShrink='0'),
            spacing(padding=box('19px', '14px', '20px', '20px')), layout(direction='column', gap='9px', align='stretch', justify='center')),
    ], hide_on(tablet=True, phone=True), layout(direction='row', gap=CELL_GAP, align='stretch', wrap='nowrap'), sizing(flexShrink='0'))


def menu():
    d = T.MENU
    panel = [
        group('Testata del menù', [
            heading('Titolo', d['title'], 'h2', attrs(cls='tp-lettere'), sizing(flexGrow='1', maxWidth=R('7.4em', tablet='100%')),
                    **display_font(BIG, R(BROWN, tablet=BORDEAUX), line='1.02em')),
            legend(),
        ], layout(direction='row', gap='32px', justify='space-between', align='flex-end', wrap='wrap'),
            spacing(padding=box('0px', SIDE, R('50px', tablet='24px'), SIDE))),
        picture('Foto (tablet e telefono)', d['feature'][0], hide_on(desktop=True), full=True, aspect=(358, 322), rad='18px',
                margin=box('0px', R(SIDE['tablet'], phone=SIDE['phone']), '20px', R(SIDE['tablet'], phone=SIDE['phone']))),
        dish_list('Piatti', d['dishes']),
        menu_head(d['groups'][0], GREEN),
        dish_list('Contorni', d['groups'][0]['rows'], table=False, color=GREEN),
        menu_head(d['groups'][1], BORDEAUX),
        picture('Foto dei dolci (tablet e telefono)', d['feature'][1], hide_on(desktop=True), full=True, aspect=(358, 322), rad='18px',
                margin=box('0px', R(SIDE['tablet'], phone=SIDE['phone']), '20px', R(SIDE['tablet'], phone=SIDE['phone']))),
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


def home():
    return document([hero(), strip(), intro(), alberto(), choice(), menu()])


def write(name, content):
    path = os.path.join(ROOT, 'content', name)
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf8', newline='\n') as handle:
        handle.write(content)
    print('%s: %d bytes, %d modules' % (name, len(content.encode('utf8')), content.count('<!-- wp:divi/')))


if __name__ == '__main__':
    write('home.html', home())
