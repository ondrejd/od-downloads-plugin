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
            tooltip: editor.getLang('odwpdp.btn_tooltip'),
            icon: 'icon odwpdp_shortcode_1-icon',
            onclick: function() {
                editor.windowManager.open( {
                    title: editor.getLang('odwpdp.dlg_title'),
                    body: [{
                        type: 'textbox',
                        name: 'title',
                        label: editor.getLang('odwpdp.input1_lbl'),
                        value: editor.getLang('odwpdp.input1_val')
                    }, {
                        type: 'textbox',
                        subtype: 'number',
                        name: 'count',
                        label: editor.getLang('odwpdp.input2_lbl'),
                        value: 5
                    }, {
                        type: 'listbox',
                        name: 'orderby',
                        label: editor.getLang('odwpdp.input3_lbl'),
                        value: 'title',
                        values: [
                            { text: editor.getLang('odwpdp.input3_opt1'), value: 'title' },
                            { text: editor.getLang('odwpdp.input3_opt2'), value: 'puton_date' },
                            { text: editor.getLang('odwpdp.input3_opt3'), value: 'putoff_date' },
                            { text: editor.getLang('odwpdp.input3_opt4'), value: 'count' }
                        ]
                    }, {
                        type: 'listbox',
                        name: 'order',
                        label: editor.getLang('odwpdp.input4_lbl'),
                        value: 'ASC',
                        values: [
                            { text: editor.getLang('odwpdp.input4_opt1'), value: 'DESC' },
                            { text: editor.getLang('odwpdp.input4_opt2'), value: 'ASC' }
                        ]
                    }, {
                        type: 'checkbox',
                        name: 'show_title',
                        label: editor.getLang('odwpdp.input5_lbl'),
                        checked: true
                    }, {
                        type: 'checkbox',
                        name: 'enable_sort',
                        label: editor.getLang('odwpdp.input6_lbl'),
                        checked: true
                    }, {
                        type: 'checkbox',
                        name: 'show_pagination',
                        label: editor.getLang('odwpdp.input7_lbl'),
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
                        ret += ']';

                        editor.insertContent(ret);
                    }
                });
            }
        });
    });
})();