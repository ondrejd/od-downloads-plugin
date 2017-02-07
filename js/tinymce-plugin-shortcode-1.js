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
                        type: 'checkbox',
                        name: 'show_title',
                        label: 'Zobrazit název?'
                    }, {
                        type: 'textbox',
                        subtype: 'number',
                        name: 'count',
                        label: 'Počet',
                        description: 'Počet položek k zobrazení (0 pro zobrazení všech položek).',
                        value: 0
                    }, {
                        type: 'listbox',
                        name: 'orderby',
                        label: 'Řadit dle',
                        description: 'Vyberte dle jaké hodnoty se mají soubory řadit.',
                        value: 'title'
                        values: [
                            { text: 'Názvu', value: 'title' },
                            { text: 'Data vyvěšení', value: 'puton_date' },
                            { text: 'Data sejmutí', value: 'putoff_date' },
                        ]
                    }],
                    onsubmit: function( e ) {
                        var ret = '[soubory_ke_stazeni';
                        if( e.data.title != "" ) {
                            ret = ret + ' title="' + e.data.title + '"';
                        }
                        ret = ret + ' show_title="' + ( e.data.show_title === true ? '1' : '0' ) + '"';
                        
                        var cnt = Number.parseInt( e.data.count );
                        if( cnt < 0 ) {
                            cnt = 0;
                        }
                        ret = ret + ' count="' + cnt + '"]';
                        console.log(e.data);
                        editor.insertContent(ret);
                    }
                });
            }
        });
    });
})();