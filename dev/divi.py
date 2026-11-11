"""Divi 5 block markup, written from Python.

Every piece of the site is a native Divi module and every size, space, colour and font is an attribute
of that module, the same one Divi's own panels read and write. Nothing here is specific to the theme:
`build.py` holds the pages.

A value can change with the device or on hover: R('64px', tablet='44px', phone='32px', hover=...).
Tablet values also hold on phones until a phone value replaces them, as in Divi.
"""
import hashlib
import json

BUILDER_VERSION = '5.15.1'
_NONE = object()
_ids = {}


class R(dict):
    """A value for computers, with other values for tablets, phones or the mouse over."""

    def __init__(self, desktop=_NONE, tablet=_NONE, phone=_NONE, hover=_NONE):
        super().__init__()
        for key, value in (('desktop', desktop), ('tablet', tablet), ('phone', phone), ('hover', hover)):
            if value is not _NONE:
                self[key] = value


def _pick(value, key):
    if isinstance(value, R):
        return value.get(key, _NONE)
    if isinstance(value, dict):
        out = {}
        for name, item in value.items():
            picked = _pick(item, key)
            if picked is not _NONE:
                out[name] = picked
        return out if out else _NONE
    return value if key == 'desktop' else _NONE


def rv(value):
    """{'size': R('64px', phone='32px'), 'color': '#fff'} -> Divi's desktop / tablet / phone shape."""
    out = {}
    desktop, hover = _pick(value, 'desktop'), _pick(value, 'hover')
    if desktop is not _NONE or hover is not _NONE:
        out['desktop'] = {}
        if desktop is not _NONE:
            out['desktop']['value'] = desktop
        if hover is not _NONE:
            out['desktop']['hover'] = hover
    for key in ('tablet', 'phone'):
        picked = _pick(value, key)
        if picked is not _NONE:
            out[key] = {'value': picked}
    return out


def merge(*dicts):
    out = {}
    for item in dicts:
        if not item:
            continue
        for key, value in item.items():
            if isinstance(value, dict) and isinstance(out.get(key), dict):
                out[key] = merge(out[key], value)
            else:
                out[key] = value
    return out


def at(path, value, responsive=True):
    """at('module.decoration.sizing', {...}): the attribute at that path, in Divi's device shape."""
    out = rv(value) if responsive else value
    for key in reversed(path.split('.')):
        out = {key: out}
    return out


def uid(seed):
    """Ten letters and digits, the same for the same seed: Divi's ids for attributes and interactions."""
    n = _ids.get(seed, 0)
    _ids[seed] = n + 1
    digest = hashlib.sha1(('%s#%d' % (seed, n)).encode('utf8')).hexdigest()
    alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789'
    number = int(digest, 16)
    out = ''
    for _ in range(10):
        number, index = divmod(number, len(alphabet))
        out += alphabet[index]
    return out


# ---------------------------------------------------------------------------------------------
# Option groups (each returns attributes to merge into a module).

def label(text):
    return at('module.meta.adminLabel', text)


def attrs(cls=None, id=None, **more):
    """Advanced > Attributes: class, id and any other attribute of the module's main element."""
    items = []
    pairs = [('id', id), ('class', cls)] + list(more.items())
    for name, value in pairs:
        if value:
            items.append({'id': uid('attr:%s:%s' % (name, value)), 'name': name.replace('_', '-'), 'value': value, 'adminLabel': '', 'targetElement': 'main'})
    return at('module.decoration.attributes', {'attributes': items}) if items else {}


def box(top=None, right=None, bottom=None, left=None):
    """Sides of a padding or a margin, as in CSS: box('10px') all four, box('10px', '20px') vertical and horizontal."""
    if right is None and bottom is None and left is None:
        right = bottom = left = top
    elif bottom is None and left is None:
        bottom, left = top, right
    elif left is None:
        left = right
    out = {}
    for name, value in (('top', top), ('right', right), ('bottom', bottom), ('left', left)):
        if value is not None and value != '':
            out[name] = value
    same = lambda a, b: a is not None and a == b and not isinstance(a, R)
    out['syncVertical'] = 'on' if same(top, bottom) else 'off'
    out['syncHorizontal'] = 'on' if same(left, right) else 'off'
    return out


def _is_sides(value):
    return isinstance(value, dict) and not isinstance(value, R)


def spacing(padding=None, margin=None, element='module'):
    value = {}
    if padding is not None:
        value['padding'] = padding if _is_sides(padding) else box(padding)
    if margin is not None:
        value['margin'] = margin if _is_sides(margin) else box(margin)
    return at(element + '.decoration.spacing', value)


def sizing(element='module', **props):
    """width, maxWidth, minWidth, height, minHeight, maxHeight, alignment, flexGrow, flexShrink, alignSelf, flexType,
    gridColumnSpan..."""
    return at(element + '.decoration.sizing', props)


def layout(direction=None, gap=None, row_gap=None, justify=None, align=None, wrap=None, display='flex', element='module', **more):
    value = {'display': display}
    if direction is not None:
        value['flexDirection'] = direction
    if gap is not None:
        value['columnGap'] = gap
    if row_gap is not None or gap is not None:
        value['rowGap'] = row_gap if row_gap is not None else gap
    if justify is not None:
        value['justifyContent'] = justify
    if align is not None:
        value['alignItems'] = align
    if wrap is not None:
        value['flexWrap'] = wrap
    value.update(more)
    return at(element + '.decoration.layout', value)


def grid(columns, gap=None, row_gap=None, element='module', **more):
    """A grid of equal columns: grid(R(3, tablet=2, phone=1), '24px')."""
    value = {'display': 'grid', 'gridColumnWidths': 'equal', 'gridColumnCount': columns}
    if gap is not None:
        value['columnGap'] = gap
        value['rowGap'] = row_gap if row_gap is not None else gap
    value.update(more)
    return at(element + '.decoration.layout', value)


def background(color=None, image=None, video=None, element='module', **more):
    value = {}
    if color is not None:
        value['color'] = color
    if image is not None:
        value['image'] = image if isinstance(image, dict) else {'url': image}
    if video is not None:
        value['video'] = video
    value.update(more)
    return at(element + '.decoration.background', value)


def radius(top_left, top_right=None, bottom_right=None, bottom_left=None):
    if top_right is None:
        top_right = bottom_right = bottom_left = top_left
    same = top_left == top_right == bottom_right == bottom_left
    return {'topLeft': top_left, 'topRight': top_right, 'bottomRight': bottom_right, 'bottomLeft': bottom_left, 'sync': 'on' if same else 'off'}


def border(rad=None, width=None, color=None, style='solid', sides=None, element='module'):
    """border(rad='16px', width='2px', color='#c5b197'); sides={'bottom': {'width': '2px', 'color': ...}} for single sides."""
    value = {}
    if rad is not None:
        value['radius'] = rad if _is_sides(rad) else radius(rad)
    styles = {}
    if width is not None or color is not None:
        line = {}
        if width is not None:
            line['width'] = width
        if color is not None:
            line['color'] = color
        line['style'] = style
        styles['all'] = line
    if sides:
        for side, line in sides.items():
            styles[side] = merge({'style': style}, line)
    if styles:
        value['styles'] = styles
    return at(element + '.decoration.border', value)


def shadow(horizontal='0px', vertical='2px', blur='18px', spread='0px', color='rgba(0,0,0,0.3)', element='module'):
    return at(element + '.decoration.boxShadow', {'style': 'preset1', 'horizontal': horizontal, 'vertical': vertical, 'blur': blur, 'spread': spread, 'color': color, 'position': 'outer'})


def font(path, **props):
    """family, weight, size, lineHeight, letterSpacing, color, textAlign, style (['uppercase'...]), ..."""
    return at(path, props)


def position(mode, vertical=None, horizontal=None, origin='top left', element='module'):
    value = {'mode': mode, 'origin': {}, 'offset': {}}
    if isinstance(mode, R):
        value['origin'] = R(**{k: {m: origin} for k, m in mode.items()})
    else:
        value['origin'] = {mode: origin}
    if vertical is not None:
        value['offset']['vertical'] = vertical
    if horizontal is not None:
        value['offset']['horizontal'] = horizontal
    if not value['offset']:
        del value['offset']
    return at(element + '.decoration.position', value)


def z_index(value, element='module'):
    return at(element + '.decoration.zIndex', value)


def transform(rotate=None, scale=None, translate=None, origin=None, element='module'):
    value = {}
    if rotate is not None:
        value['rotate'] = {'z': rotate}
    if scale is not None:
        value['scale'] = scale if _is_sides(scale) else {'x': scale, 'y': scale}
    if translate is not None:
        value['translate'] = translate
    if origin is not None:
        value['origin'] = origin
    return at(element + '.decoration.transform', value)


def overflow(x='hidden', y=None, element='module'):
    return at(element + '.decoration.overflow', {'x': x, 'y': y if y is not None else x})


def hide_on(desktop=False, tablet=False, phone=False):
    """Advanced > Visibility: where the module is not shown."""
    flag = lambda b: 'on' if b else 'off'
    return at('module.decoration.disabledOn', R(flag(desktop), tablet=flag(tablet), phone=flag(phone)))


def animation(style='fade', direction='center', duration='700ms', delay='0ms', intensity='12%', opacity='0%', curve='ease-out'):
    """Design > Animation: the module comes in when it enters the screen."""
    value = {'style': style, 'duration': duration, 'delay': delay, 'startingOpacity': opacity, 'speedCurve': curve, 'repeat': 'once'}
    if style != 'fade':
        value['direction'] = direction
        value['intensity'] = {style: intensity}
    return at('module.decoration.animation', value)


# ---------------------------------------------------------------------------------------------
# Blocks.

def _encode(attributes):
    text = json.dumps(attributes, ensure_ascii=False, separators=(',', ':'))
    # As WordPress writes block attributes (serialize_block_attributes).
    return text.replace('--', '\\u002d\\u002d').replace('<', '\\u003c').replace('>', '\\u003e').replace('&', '\\u0026').replace('\\"', '\\u0022')


def block(name, *attributes, children=None):
    data = merge(*attributes, {'builderVersion': BUILDER_VERSION})
    if children is None:
        return '<!-- wp:%s %s /-->' % (name, _encode(data))
    return '<!-- wp:%s %s -->\n%s\n<!-- /wp:%s -->' % (name, _encode(data), '\n\n'.join(children), name)


def document(blocks):
    return '<!-- wp:divi/placeholder -->' + '\n\n'.join(blocks) + '<!-- /wp:divi/placeholder -->'


def section(name, children, *attributes):
    return block('divi/section', label(name), *attributes, children=children)


def row(name, children, *attributes, columns=1):
    structure = at('module.advanced.flexColumnStructure', 'equal-columns_%d' % columns)
    return block('divi/row', label(name), structure, *attributes, children=children)


def column(children, *attributes, size='24_24', name=None):
    """size: twenty-fourths of the row, '12_24' a half; R('12_24', tablet='24_24') to stack on tablets."""
    return block('divi/column', label(name) if name else {}, sizing(flexType=size), *attributes, children=children)


def group(name, children, *attributes):
    return block('divi/group', label(name), *attributes, children=children)


def heading(name, text, level, *attributes, **font_props):
    return block('divi/heading', label(name), at('title.innerContent', text),
                 at('title.decoration.font.font', merge({'headingLevel': level}, font_props)), *attributes)


def text(name, html, *attributes, headings=None, **font_props):
    """headings={'h2': {...font...}} styles the titles written inside the text."""
    extra = [at('content.decoration.bodyFont.body.font', font_props)] if font_props else []
    for tag, props in (headings or {}).items():
        extra.append(at('content.decoration.headingFont.%s.font' % tag, props))
    return block('divi/text', label(name), at('content.innerContent', html), *extra, *attributes)


def _ratio(value):
    if isinstance(value, R):
        return R(**{key: _ratio(item) for key, item in value.items()})
    return {'width': str(value[0]), 'height': str(value[1])}


def image(name, src, alt='', *attributes, title=None, link=None, target='off', width=None, max_width=None, aspect=None, fit=None,
          align=None, margin=None, rad=None, full=False, bottom_space=None):
    """An Image module. Its sizes, spaces and corners are kept by Divi in places of their own:
    width / max_width / aspect ((4, 3): the photo is cut to that shape) / full (as wide as its column),
    margin, align ('left', 'center', 'right'), rad (round corners)."""
    content = {'src': src, 'alt': alt}
    if title:
        content['titleText'] = title
    if link:
        content['linkUrl'] = link
        content['linkTarget'] = target
    size = {}
    if width is not None:
        size['width'] = width
    if max_width is not None:
        size['maxWidth'] = max_width
    if aspect is not None:
        size['aspectRatio'] = _ratio(aspect)
    if full:
        size['forceFullwidth'] = 'on'
    extra = []
    if size:
        extra.append(at('module.advanced.sizing', size))
    if aspect is not None or fit:
        extra.append(at('image.decoration.fit', {'objectFit': fit or 'cover', 'objectPosition': 'center center'}))
    space = {}
    if margin is not None:
        space['margin'] = margin if _is_sides(margin) else box(margin)
    if bottom_space is not None:
        space['showBottomSpace'] = 'on' if bottom_space else 'off'
    if space:
        extra.append(at('module.advanced.spacing', space))
    if align is not None:
        extra.append(at('module.advanced.align', align))
    if rad is not None:
        extra.append(border(rad=rad, element='image'))
    return block('divi/image', label(name), at('image.innerContent', content), *extra, *attributes)


def button(name, text_, url, *attributes, target='off', icon=None, **font_props):
    """icon: {'unicode': '&#x24;', 'type': 'divi', 'weight': '400'} or None for a button without one."""
    settings = {'enable': 'on', 'icon': {'enable': 'off'}}
    if icon:
        settings['icon'] = {'enable': 'on', 'settings': icon, 'placement': 'right', 'onHover': 'off'}
    extra = [at('button.decoration.font.font', font_props)] if font_props else []
    return block('divi/button', label(name), at('button.innerContent', {'text': text_, 'linkUrl': url, 'linkTarget': target}),
                 at('button.decoration.button', settings), *extra, *attributes)


def link(name, text_, url, *attributes, target='off', **font_props):
    extra = [at('content.decoration.font.font', font_props)] if font_props else []
    return block('divi/link', label(name), at('content.innerContent', {'text': text_, 'linkUrl': url, 'linkTarget': target}), *extra, *attributes)


def icon(name, unicode_, *attributes, kind='divi', weight='400', color=None, size=None):
    extra = []
    if color is not None:
        extra.append(at('icon.advanced.color', color))
    if size is not None:
        extra.append(at('icon.advanced.size', size))
    return block('divi/icon', label(name), at('icon.innerContent', {'unicode': unicode_, 'type': kind, 'weight': weight}), *extra, *attributes)
