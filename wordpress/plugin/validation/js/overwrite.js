(function ($) {
    "use strict";
    var Override = {
        cache: {
			'form': {}, 
			'load': {}, 
			'views': {}
		},
        
        form: {
            fieldset: function(el, htmlOptions){
                htmlOptions = $.extend( true, {}, { 'fieldset': {'template': Override.getView( 'fieldset' )} }, ( htmlOptions || {} ) );
                
                var view = Override.get( 'fieldset.template', htmlOptions ); 
                if( !view || !Override.cache[ 'load' ][ view ] ) {
                    Override.debug( 'Form.Fieldset: `' + view + '` Not Found!' );
                    return el;
                }
                
                return Override.cache[ 'load' ][ view ]({
                    'element'        : el,
                    'fieldset'       : _.omit(htmlOptions.fieldset, 'template'),
                    'htmlOptions'    : _.omit(htmlOptions, 'fieldset')
                });
            },
            
            textfield: function( name, value, htmlOptions ){
                return this.fieldset(
                    this.inputField( 'text', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            numberField: function( name, value, htmlOptions ) {
                return this.fieldset(
                    this.inputField( 'number', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            rangeField: function( name, value, htmlOptions ) {
                return this.fieldset(
                    this.inputField( 'range', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            dateField: function( name, value, htmlOptions ) {
                return this.fieldset(
                    this.inputField( 'date', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            timeField: function( name, value, htmlOptions ) {
                return this.fieldset(
                    this.inputField( 'time', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            dateTimeField: function( name, value, htmlOptions ) {
                return this.fieldset(
                    this.inputField( 'datetime', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            dateTimeLocalField: function( name, value, htmlOptions ) {
                return this.fieldset(
                    this.inputField( 'datetime-local', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            weekField: function( name, value, htmlOptions ) {
                return this.fieldset(
                    this.inputField( 'week', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            emailField: function( name, value, htmlOptions) {
                return this.fieldset(
                    this.inputField( 'email', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            telField: function( name, value, htmlOptions ) {
                return this.fieldset(
                    this.inputField( 'tel', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            urlField: function( name, value, htmlOptions ) {
                return this.fieldset(
                    this.inputField( 'url', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            hiddenField: function( name, value, htmlOptions ) {
                return this.inputField( 'hidden', name, value, _.omit(htmlOptions, 'fieldset') );
            },
            
            passwordField: function( name, value, htmlOptions ) {
                return this.fieldset(
                    this.inputField( 'password', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            fileField: function( name, value, htmlOptions ) {
                return this.fieldset(
                    this.inputField( 'file', name, value, _.omit(htmlOptions, 'fieldset') ),
                    htmlOptions
                );
            },
            
            textArea: function( name, value, htmlOptions ) {
                var htmlOptions = $.extend( true, {} , { 'name': name }, ( htmlOptions || {} ));
                
                if( !_.has(htmlOptions, 'id') ) {
                    htmlOptions['id'] = this.toIdByName( name );
                } else if( htmlOptions['id'] == false ){
                    htmlOptions = _.omit(htmlOptions, 'id')
                }
                
                return this.fieldset(
                    this.tag( 'textarea', _.omit(htmlOptions, 'fieldset'), value ),
                    htmlOptions
                );
            },
            
            radioButton: function( name, checked, htmlOptions ) {
                htmlOptions = (htmlOptions || {});
                
                if(checked)
                    htmlOptions['checked']='checked';
                else
                    delete htmlOptions['checked'];
                    
                var value   = (htmlOptions['value'] || 1);
                var uncheck = null;
                var hidden  = '';
                
                if( _.has(htmlOptions, 'uncheckValue') ) {
                    uncheck = htmlOptions['uncheckValue'];
                    delete htmlOptions['uncheckValue'];
                }
                
                if( uncheck !== null ) {
                    hidden = this.hiddenField( name, uncheck );
                }
                
                return this.fieldset(
                    (hidden + this.inputField( 'radio', name, value, _.omit(htmlOptions, 'fieldset') )),
                    htmlOptions
                );
            },
            
            checkBox: function( name, checked, htmlOptions ) {
                htmlOptions = (htmlOptions || {});
                
                if( checked )
                    htmlOptions['checked']='checked';
                else
                    delete htmlOptions['checked'];
                    
                var value   = ( htmlOptions['value'] || 1 );
                var uncheck = null;
                var hidden  = '';
                
                if( _.has( htmlOptions, 'uncheckValue' ) ) {
                    uncheck = htmlOptions['uncheckValue'];
                    delete htmlOptions['uncheckValue'];
                }
                    
                
                if( uncheck !== null ) {
                    hidden = this.hiddenField( name, uncheck );
                }
                
                return this.fieldset(
                    (hidden + this.inputField( 'checkbox', name, value, _.omit(htmlOptions, 'fieldset') )),
                    htmlOptions
                );
            },
            
            dropDownList: function( name, value, htmlOptions, data ) {
                var htmlOptions = $.extend( true, {}, { 'name': name }, ( htmlOptions || {} ) );
                
                if( !_.has(htmlOptions, 'id') ) {
                    htmlOptions['id'] = this.toIdByName( name );
                } else if( htmlOptions['id'] == false ){
                    htmlOptions = _.omit(htmlOptions, 'id')
                }
                    
                var options = "\n" + this.listOptions( value, data, htmlOptions );
                var hidden = '';
                
                if( htmlOptions['multiple'] ) {
                    if( htmlOptions['name'].substr(-2) !== '[]' ) {
                        htmlOptions['name'] += '[]';
                    }
                    
                    if( _.has(htmlOptions, 'unselectValue')) {
                        hidden = this.hiddenField( htmlOptions['name'].substr( 0, htmlOptions['name'].length - 2 ), htmlOptions['unselectValue'] );
                        delete htmlOptions['unselectValue'];
                    }
                }
                
                return this.fieldset(
                    (hidden + this.tag( 'select', _.omit(htmlOptions, 'fieldset'), options )),
                    htmlOptions
                );
            },
            
            listBox: function( name, value, data, htmlOptions ) {
                if( !_.has(htmlOptions, 'size') ) {
                    htmlOptions['size'] = 4;
                }
                
                if( htmlOptions['multiple'] ) {
                    if( name.substr(-2) !== '[]' ) {
                        name += '[]';
                    }
                }
                
                return this.dropDownList( name, value, htmlOptions, data );
            },
            
            checkBoxList: function( name, value, htmlOptions, data ) {
                var self = this;
                var template = ( htmlOptions['template'] || '{input} {label}' );
                var separator = ( htmlOptions['separator'] || this.tag('br') );
                
                delete htmlOptions['template'];
                delete htmlOptions['separator'];
               
                if( name.substr(-2) !=='[]' ) {
                    name += '[]';
                }
                
                var labelOptions = (htmlOptions['labelOptions'] || {});
                delete htmlOptions['labelOptions'];
                
                var baseID = ( htmlOptions['baseID'] || this.toIdByName( name ) );
                delete htmlOptions[ 'baseID' ];
                
                var checkAll = true;
                var items = [];
                var id = 0;
                
                _.each(data, function(labelTitle, val){
                    var checked = ( ( !_.isArray(value) && value == val ) || ( _.isArray(value) && self.inArray(value, val) ) );
                    var checkAll = (checkAll && checked);
                    
                    htmlOptions['value'] = val;
                    htmlOptions['id']    = baseID + '_' + (id++);
                    var option           = self.checkBox( name, checked, _.omit(htmlOptions, 'fieldset') );
                    var beginLabel       = self.openTag( 'label', labelOptions );
                    var label            = self.label( labelTitle, htmlOptions['id'], labelOptions );
                    var endLabel         = self.closeTag( 'label' );
                    
                    items.push(template.replaceArray(
                        ['{input}', '{beginLabel}', '{label}', '{labelTitle}', '{endLabel}'],
                        [option, beginLabel, label,  labelTitle, endLabel]
                    ));
                });
                
                return this.fieldset(
                    items.join(separator),
                    htmlOptions
                );
            },
            
            radioButtonList: function( name, value, htmlOptions, data ) {
                var self = this;
                var template  = htmlOptions['template']  || '{input} {label}';
                var separator = htmlOptions['separator'] || this.tag('br');
                
                delete htmlOptions['template'];
                delete htmlOptions['separator'];
                
                var labelOptions = htmlOptions['labelOptions'] || {};
                delete htmlOptions['labelOptions'];
                
                if( htmlOptions['empty'] ) {
                    if( !_.isString(htmlOptions['empty']) )
                        htmlOptions['empty'] = { '' : htmlOptions['empty'] };
                    
                    data = $.extend( true, {}, htmlOptions['empty'], data );
                    delete htmlOptions['empty'];
                }
                
                var id = 0;
                var items = [];
                var baseID = htmlOptions['baseID'] || this.toIdByName(name);
                delete htmlOptions['baseID'];
                
                _.each(data, function(labelTitle, val){
                    htmlOptions['value']     = value;
                    htmlOptions['id']        = baseID + '_' + (id++);
                    
                    var checked    = (value == val);
                    var option     = self.radioButton( name, checked, _.omit(htmlOptions, 'fieldset') );
                    var beginLabel = self.openTag( 'label', labelOptions );
                    var label      = self.label( labelTitle, htmlOptions['id'], labelOptions );
                    var endLabel   = self.closeTag('label');
                    
                    items.push( template.replaceArray(
                        ['{input}', '{beginLabel}', '{label}', '{labelTitle}', '{endLabel}'],
                        [option, beginLabel, label, labelTitle, endLabel]
                    ));
                });
                
                return this.fieldset(
                    items.join(separator),
                    htmlOptions
                );
            },
            
            listOptions: function(selection, listData, htmlOptions ) {
                var self = this;
                var content = '';
                
                if( htmlOptions['prompt'] ) {
                    content += self.tag( 'option', {'value': ''}, htmlOptions['prompt'] ) + "\n";
                    delete htmlOptions['prompt'];
                }
                
                if( _.has(htmlOptions, 'empty') ) {
                    if( _.isString( htmlOptions['empty'] ) ) {
                        htmlOptions['empty'] = {'': htmlOptions['empty']};
                    }
                    
                    _.each(htmlOptions['empty'], function(label, value){
                        content += self.tag( 'option', {'value': value}, label ) + "\n";
                    });
                    
                    delete htmlOptions['empty'];
                }
                
                var options = [];
                if( htmlOptions['options'] ) {
                    options = htmlOptions['options'];
                    delete htmlOptions['options'];
                }
                
                var key = (htmlOptions['key'] || 'primaryKey');
                
                if( _.isArray(selection) ) {
                    _.each(selection, function(item, i){
                        if(_.isObject(item)) {
                            selection[i] = item[key];
                        }
                    });
                }
                else if( _.isObject(selection) ) {
                    selection = selection[key];
                }
                
                _.each(listData, function(value, key){
                    if( _.isObject( value ) ) {
                        content += self.tag(
                            'optgroup', 
                            {'label': key},
                            self.listOptions(selection, value, {'options': options})
                        ) + "\n";
                    }
                    else {
                        var attributes = {'value': key};
                        if( ( !_.isArray( selection ) && ( key == selection ) ) || ( _.isArray( selection ) && self.inArray(selection, key) ))
                            attributes['selected']='selected';
                        
                        if( options[ key ] )
                            attributes = $.extend( true, {}, attributes, options[ key ] );
                        
                        content += self.tag('option', attributes,  value) + "\n";
                    }
                });
                
                delete htmlOptions['key'];
                
                return content;
            },
            
            inputField: function( type, name, value, $htmlOptions ){
                var htmlOptions = $.extend( true, {}, { 'type': type, 'value': value, 'name': name }, ( $htmlOptions || {} ) );
                
                if( !_.has(htmlOptions, 'id') ) {
                    htmlOptions['id'] = this.toIdByName( name );
                } else if( htmlOptions['id'] == false ){
                    htmlOptions = _.omit(htmlOptions, 'id')
                }
                
                return this.tag('input', htmlOptions);
            },
            
            label: function(label, $for, htmlOptions) {
                if($for===false)
                    delete htmlOptions['for'];
                else
                    htmlOptions['for'] = $for;
                
                return this.tag( 'label', htmlOptions, label );
            },
            
            tag: function(tag, htmlOptions, content, closeTag){
                var html ='<' + tag + this.renderAttributes( htmlOptions );
                if( !content ) {
                    return ( closeTag === undefined || closeTag ) ? html + ' />' : html + '>';
                } else {
                    return ( closeTag === undefined || closeTag ) ? html + '>' + content + '</' + tag + '>' : html + '>' + content;
                }
            },
            
            openTag: function(name, htmlOptions){
                var html = this.tag(name, htmlOptions);
                return html.substr(0, (html.length - (name.length + 3)));
            },
            
            closeTag: function(name){
                return '</' + name + '>';
            },
            
            toIdByName: function(name) {
                return name.replace('[]', '').replace('][', '_').replace('[', '_').replace('[', '_');
            },
            
            renderAttributes: function( htmlOptions ) {
                var specialAttributes = {
                    'autofocus':1,
                    'autoplay':1,
                    'async':1,
                    'checked':1,
                    'controls':1,
                    'declare':1,
                    'default':1,
                    'defer':1,
                    'disabled':1,
                    'formnovalidate':1,
                    'hidden':1,
                    'ismap':1,
                    'itemscope':1,
                    'loop':1,
                    'multiple':1,
                    'muted':1,
                    'nohref':1,
                    'noresize':1,
                    'novalidate':1,
                    'open':1,
                    'readonly':1,
                    'required':1,
                    'reversed':1,
                    'scoped':1,
                    'seamless':1,
                    'selected':1,
                    'typemustmatch':1,
                };
                
                if( _.isEmpty( htmlOptions ) )
                    return '';
                
                var html = '';
                var self = this;
                
                _.each( htmlOptions, function( value, name ){
                    if( specialAttributes[ name ] ) {
                        if( value===false ) {
                            html += ' ' + name + '="false"';
                        } else if( value ) {
                            html += ' ' + name + '="' + name + '"';
                        }
                    } else if( value !== null ) {
                        html += ' ' + name + '="' + self.escape( value ) + '"';
                    }
                });
                
                return html;
            },
            
            escape: function(s) {
                return ('' + s) /* Forces the conversion to string. */
                    .replace(/\\/g, '\\\\') /* This MUST be the 1st replacement. */
                    .replace(/    /g, '\    ') /* These 2 replacements protect whitespaces. */
                    .replace(/\n/g, '\\n')
                    .replace(/\u00A0/g, '\\u00A0') /* Useful but not absolutely necessary. */
                    .replace(/&/g, '\\x26') /* These 5 replacements protect from HTML/XML. */
                    .replace(/'/g, '\\x27')
                    .replace(/"/g, '\\x22')
                    .replace(/</g, '\\x3C')
                    .replace(/>/g, '\\x3E')
                    ;
            },
            
            inArray: function(data, val){
                var exists = false;
                
                _.each( data, function( value ){
                    if( value == val ) {
                         exists = true;
                    }
                });
                
                return exists;
            }
        },
        
        get: function (key, data) {
            var keys = key.split( '|' );
            var params = [];
            var fn = null;
             
            if( keys && keys.length == 2 ){
                key = keys[0];
                fn = keys[1];
                
                if( fn.indexOf( ':' ) >= 0 ){
                    var _fn = fn;
                    var _parts = _fn.split( ':' );
                    
                    fn = _parts[0];
                    params = _fn.match( /['"]([^'"]+?)['"]/g );
                }
            }
            
            var keys = key.split( '.' );
            var curr = data;
            var self = this;
            
            for (var i = 0, len = keys.length; i < len; i++) {
                var mt = keys[i].match( /^([^\(]+)\(([^\)]+)\)$/ );
                var filter = null;
                
                if( mt ) {
                    keys[ i ] = mt[ 1 ];
                    filter = JSON.parse( mt[ 2 ] );
                }
                
                if( _.isNull( curr ) || _.isUndefined( curr[ keys[ i ] ] ) ) {
                    return undefined;
                }
                else {
                    curr = curr[ keys[ i ] ];
                }
                
                if( !_.isEmpty( filter ) && _.isArray( curr )) {
                    curr = _.filter( curr, _.matcher( filter ) );
                }
            }
            
            return fn ? this.utils[ fn ].apply( this, [ curr ].concat( params ) ) : curr;
        },
        
        query: function( url ){
            url = (url || window.location.href);
            var params = {};
            
            url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
               params[ key ] = value;
            });
            
            return params;
        },
        
        redirect: function(url, fn){
            if( !_.isString( url ) ) {
                 url = $(url.currentTarget).attr("href");
            }
            
            var parser = document.createElement('a');
            parser.href = url;
            
            if( parser.pathname == window.location.pathname ) {
                window.history.pushState({}, null, url);
                fn.call();
            } else {
                window.location.href = url;
            }
        },
        
        makeUrl: function(url, params){
            var parameters = {};
            
            var parser = document.createElement('a');
            parser.href = (( url == 'current') ? location.href : url);
            
            var search = parser.search.replace( /^\?|#.*$/g, '' );
            if( search ) {
                var i, nv;
                var parts = search.split('&');
                for (i = 0; i < parts.length; i++) {
                    nv = parts[ i ].split('=');
                    parameters[ decodeURIComponent( nv[ 0 ] ) ] = decodeURIComponent( nv[ 1 ] );
                }
            }
            
            if( params ) {
                _.each(params, function( value, key ){
                    parameters[  key ] = value;
                });
            }
            
            var list = [];
            _.each(parameters, function(value, key){
                list.push( encodeURIComponent( key ) + '=' + encodeURIComponent( value ) );
            })
            
            parser.search = '?' + list.join('&');
            return parser.href;
        },
        
        render: function(view, data, fn, urls){
            var self = this;
            urls = urls || [];
            
            /**
             * {$form: [ [type, [name, value, htmlOptions, data]], ]}
             */
            if( _.has( data, '$form' ) ) {
                _.each( data[ '$form' ], function( row ){
                    var fieldset = Override.get( '1.2.fieldset.template', row );
                    if( fieldset ){
                        urls.push( fieldset );
                    }
                });
				
                urls.push( self.getView( 'fieldset' ) );
            }
            
            /**
             * {$listView: [result, columns, actions, options]}
             */
            if( _.has( data, '$listView' ) ) {
                var listView = Override.get( '$listView.3.template', data );
                if( !listView ) {
                    urls.push( self.getView( 'listView' ) );
                } else {
                    urls.push( listView );
                }
            }
            
            /**
             * {$partial: {selector: {template: '', params: {}}}}
             */
            if( _.has( data, '$partial' ) ) {
                _.each( data[ '$partial' ], function( row ){
                    if( !_.isEmpty( row[ 'template' ] ) ){
                        urls.push( row.template );
                    }
                });
            }
            
            self.load( _.union( urls, [ view ] ), function(){
                if( _.has( data, '$form' ) ) {
                    var form = {};
                    _.each( data[ '$form' ], function( row ){
                        form[ row[1][0] ] = self.form[ row[0] ].apply( self.form, row[1] );
                    });
                    
                    data[ '$form' ] = form;
                }
                
                if( _.has( data, '$listView' ) ) {
                    data[ '$listView' ] = self.listView.apply( self, data[ '$listView' ] );
                }
                
                if( _.has( data, '$partial' ) ) {
                    _.each( data[ '$partial' ], function( row, key ){
                        data[ '$partial' ][ key ] = Override.cache[ 'load' ][ row.template ]( {'data': data, 'params': (row.params || {})} );
                    });
                }
                
                fn.call(self, Override.cache[ 'load' ][ view ]( data ))
            });
        },
        
        load: function(urls, fn){
            var self = this;
			
            urls = _.filter( _.uniq( urls ) , function( url ){
				var loaded = _.has( Override.cache[ 'load' ], url );
				
				if( !loaded && /^script\#/.test( url ) ) {
					if( $( url ).length>0 ) {
						Override.cache[ 'load' ][ url ] = _.template( $( url ).html() );
					}				
					
					loaded = true;
				}
				
				return !loaded; 
			});
            
            if( urls.length>0 ) {
                $.when.apply($, urls.map(function( url ) {
                    return $.get('skin/' + url + '.html');
                }))
                .done(function(){
                    if( urls.length == 1 ) {
                        if( arguments[ 1 ] == 'success' ) {
                            Override.cache[ 'load' ][ urls[ 0 ] ] = _.template( arguments[ 0 ] );
                        }
                    } else {
                        for (var i = 0; i < urls.length; i++) {
                            if( arguments[ i ][ 1 ] == 'success' ) {
                                Override.cache[ 'load' ][ urls[ i ] ] = _.template( arguments[ i ][ 0 ] );
                            }
                        }    
                    }
                    
                    fn.call(self);
                });
            } else {
                fn.call(self);
            }
        },
        
        listView: function(result, columns, actions, options){
            var self = this;
            
            options = _.extend({ 'template': self.getView( 'listView' ) }, ( options || {} ));
            
            var template = options.template ;
            if( !template || !Override.cache[ 'load' ][ template ] ) {
                self.debug( 'ListView: `' + template + '` Not Found!' );
                return;
            }
            
            return Override.cache[ 'load' ][ template ]( {
                'rows': result.rows,
                'pageCount': Math.ceil( result.total / result.limit ),
                'currentPage': result.page,
                'columns': (columns || {}),
                'actions': (actions || {}),
                'options': (options || {})
            } );
        },
		
		getView: function( type ){
			if( type == 'fieldset' ) {
				return (( $("script#FieldSet").length > 0 ) ? 'script#FieldSet' : 'components/fieldset' );
			} else if( type == 'listView' ) {
				return (( $("script#ListView").length > 0 ) ? 'script#ListView' : 'components/listView' );
			}
		},
        
        utils: {
            date: function(){
                return JSON.stringify(arguments);
            }
        },
        
        debug: function( message ){
            console.log( message );
        }
    };
    
    _.mixin({
        com: function(){
            return Override;
        }
    });
    
    String.prototype.replaceArray = function(find, replace) {
        var replaceString = this;
        var regex; 
        for (var i = 0; i < find.length; i++) {
            regex = new RegExp(find[i], "g");
            replaceString = replaceString.replace(regex, replace[i]);
        }
        return replaceString;
    };    
}(window.jQuery));