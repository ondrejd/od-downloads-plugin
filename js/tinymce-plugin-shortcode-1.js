/**
 * TinyMCE plugin for the shortcode "Soubory ke stažení".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-downloads_plugin
 */

(function() {
    tinymce.PluginManager.add('odwpdp_shortcode_1', function( editor, url ) {
        editor.addButton( 'odwpdp_shortcode_1', {
            tooltip: 'Soubory ke stažení',
            icon: 'icon odwpdp_shortcode_1-icon',
            onclick: function() {
                editor.windowManager.open( {
                    title: 'Vložte soubory ke stažení',
                    body: [{
                        type: 'textbox',
                        name: 'title',
                        label: 'Název',
                        value: 'Soubory ke stažení'
                    }, {
                        type: 'textbox',
                        subtype: 'number',
                        name: 'count',
                        label: 'Počet položek',
                        value: 5
                    }, {
                        type: 'listbox',
                        name: 'orderby',
                        label: 'Řadit dle',
                        value: 'title',
                        values: [
                            { text: 'Názvu', value: 'title' },
                            { text: 'Data vyvěšení', value: 'puton_date' },
                            { text: 'Data sejmutí', value: 'putoff_date' }
                        ]
                    }, {
                        type: 'listbox',
                        name: 'order',
                        label: 'Řadit',
                        value: 'ASC',
                        values: [
                            { text: 'Sestupně', value: 'DESC' },
                            { text: 'Vzestupně', value: 'ASC' }
                        ]
                    }, {
                        type: 'checkbox',
                        name: 'show_title',
                        label: 'Zobrazit název?',
                        checked: true
                    }, {
                        type: 'checkbox',
                        name: 'enable_sort',
                        label: 'Umožnit ruční řazení?',
                        checked: true
                    }, {
                        type: 'checkbox',
                        name: 'show_pagination',
                        label: 'Zobrazit stránkování?',
                        checked: true
                    }, {
                        type: 'checkbox',
                        name: 'enable_ajax',
                        label: 'Povolit AJAX?',
                        checked: true
                    }],
                    onsubmit: function( e ) {
                        var ret = '[soubory_ke_stazeni';

                        if( e.data.title != "" ) {
                            ret = ret + ' title="' + e.data.title + '"';
                        }

                        ret += Number.parseInt( e.data.count ) < 0 ? ' count="0"]' : ' count="' + e.data.count + '"';
                        ret += ' orderby="' + e.data.orderby + '"';
                        ret += ' show_title="' + ( e.data.show_title === true ? '1' : '0' ) + '"';
                        ret += ' enable_sort="' + ( e.data.enable_sort === true ? '1' : '0' ) + '"';
                        ret += ' show_pagination="' + ( e.data.show_pagination === true ? '1' : '0' ) + '"';
                        ret += ' enable_ajax="' + ( e.data.enable_ajax === true ? '1' : '0' ) + '"';
                        ret += ']';

                        editor.insertContent(ret);
                    }
                });
            }
        });
    });
})();