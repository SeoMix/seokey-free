const {
	__,
} = wp.i18n;
const {
	registerBlockType,
    createBlock
} = wp.blocks;
const {
	Button,
	PanelBody,
	TextControl,
    TextareaControl
} = wp.components;
const {
	RichText,
} = wp.blockEditor;
const {
	Fragment,
} = wp.element;

registerBlockType('seokey/faq-block',{ 
        title: 'SEOKEY FAQ',
        icon: 'format-chat',
        description: __('Add an FAQ optimised for SEO.', 'seo-key'),
        category: 'seokey-blocks',
        keywords: [
            'FAQ',
            'Seokey',
            __( 'Enter the question', 'seo-key' ),
            __( 'Enter the answer', 'seo-key' ),
        ],
        // Adds a button to convert other FAQ from othe SEO plugins
        transforms: {
            from: [
              {
                type: 'block',
                blocks: [ 'yoast/faq-block' ],
                transform: ( attributes ) => {
                    var questions = [];
                    attributes.questions.forEach( (e) => questions.push( { question : e.jsonQuestion, response : e.jsonAnswer } ) );
                    return createBlock( 'seokey/faq-block', {
                        faq: questions,
                    } );
                },
              },
            ],
        },

        attributes: {
            faq: {
                type: 'array',
                default: [],
            },
        },

        edit: ( props ) => {
            const handleAddQR = () => {
                const faq = [ ...props.attributes.faq ];
                faq.push( {
                    question: '',
                    response: ''
                } );
                props.setAttributes( { faq } );
            };
        
            const handleRemoveQR = ( index ) => {
                const faq = [ ...props.attributes.faq ];
                faq.splice( index, 1 );
                props.setAttributes( { faq } );
            };
        
            const handleQuestionChange = ( question, index ) => {
                const faq = [ ...props.attributes.faq ];
                faq[ index ].question = question;
                props.setAttributes( { faq } );
            };
            const handleResponseChange = ( response, index ) => {
                const faq = [ ...props.attributes.faq ];
                faq[ index ].response = response;
                props.setAttributes( { faq } );
            };

            const sleep = ( ms ) => {
                return new Promise(resolve => setTimeout(resolve, ms));
            }

            const handleMoveUpQR = ( index, element ) => {
                if (index !== 0) {
                    element.parentNode.parentNode.parentNode.classList.add('seokey-movingup');
                    sleep(300).then(() => { 
                        const faq = [ ...props.attributes.faq ];
                        var tmp = faq[index];
                        faq[index] = faq[index - 1];
                        faq[index - 1] = tmp;
                        props.setAttributes( { faq } );
                        element.parentNode.parentNode.parentNode.classList.remove('seokey-movingup');
                    });
                    
                }
            };
            const handleMoveDownQR = ( index, element ) => {
                if (props.attributes.faq.length - 1 !== index) {
                    element.parentNode.parentNode.parentNode.classList.add('seokey-movingdown');
                    sleep(300).then(() => { 
                        const faq = [ ...props.attributes.faq ];
                        var tmp = faq[index];
                        faq[index] = faq[index + 1];
                        faq[index + 1] = tmp;
                        props.setAttributes( { faq } );
                        element.parentNode.parentNode.parentNode.classList.remove('seokey-movingdown');
                    });
                }
            };
        
            let qrFields;
        
            if ( props.attributes.faq.length ) {
                qrFields = props.attributes.faq.map( ( qr, index ) => {
                    return <Fragment key={ index }>
                        <div>
                            <div className="seokey-faq-qr-editor">
                                <div className="seokey-faq-qr-inputs">
                                    <TextControl
                                        className="seokey-faq-question-editor"
                                        placeholder={__( 'Enter the question', 'seo-key' )}
                                        value={ qr.question }
                                        onChange={ ( question ) => handleQuestionChange( question, index ) }
                                    />
                                    <RichText
                                        tagName="p"
                                        className="seokey-faq-response-editor"
                                        placeholder={__( 'Enter the answer', 'seo-key' )}
                                        value={ qr.response }
                                        onChange={ ( response ) => handleResponseChange( response, index ) }
                                    />
                                </div>
                                <div className="seokey-faq-qr-controls">
                                    <Button
                                        className="seokey-faq-button-editor"
                                        icon="no-alt"
                                        label={__( 'Delete question', 'seo-key' )}
                                        onClick={ () => handleRemoveQR( index ) }
                                    />
                                    <Button
                                        className="seokey-faq-button-editor"
                                        icon="arrow-up-alt2"
                                        label={__( 'Move up', 'seo-key' )}
                                        onClick={ ({target}) => handleMoveUpQR( index, target ) }
                                        disabled={index === 0 ? 'true' : ''}
                                    />
                                    <Button
                                        className="seokey-faq-button-editor"
                                        icon="arrow-down-alt2"
                                        label={__( 'Move down', 'seo-key' )}
                                        onClick={ ({target}) => handleMoveDownQR( index, target ) }
                                        disabled={props.attributes.faq.length - 1 === index ? 'true' : ''}
                                    />
                                </div>
                            </div>
                        </div>
                    </Fragment>;
                } );
            }
        
            return [
                <div className={ props.className }>
                    { qrFields }
                    <Button
                        onClick={ handleAddQR.bind( this ) }
                    >
                        { __( 'Add question', 'seo-key' ) }
                    </Button>
                </div>,
            ];
        },

        save: ( props ) => {
            const qrFields = props.attributes.faq.map( ( qr, index ) => {
                return (
                    <details key={ index }>
                        <summary>{ qr.question }</summary>
                        <div>
                            <RichText.Content tagName="p" value={ qr.response } />
                        </div>
                    </details>
                );
            } );
            
            return (
                <div className={ props.className }>
                    { qrFields }
                </div>
            );
        },
    }
);