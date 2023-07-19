/* global mmApiDataDisplayBlock */

import { ToggleControl, PanelBody } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useState, useEffect } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { sprintf, __ } from '@wordpress/i18n';

export default function Edit( { attributes, setAttributes } ) {
	const [ error, setError ] = useState( null );
	const [ record, setRecord ] = useState( null );
	const [ isLoaded, setIsLoaded ] = useState( false );
	const htmlStructure = [
		<InspectorControls key="mm-api-data-display-block-inspector-controls">
			<PanelBody
				title={ __( 'Manage Table columns', 'manisha-makhija' ) }
				key="mm-api-data-display-block-panel-body"
				initialOpen={ true }
			>
				<ToggleControl
					label={ __( 'Show ID', 'manisha-makhija' ) }
					onChange={ ( value ) => {
						setAttributes( {
							id: value,
						} );
					} }
					checked={ attributes.id }
				/>
				<ToggleControl
					label={ __( 'Show First Name', 'manisha-makhija' ) }
					onChange={ ( value ) => {
						setAttributes( {
							first_name: value,
						} );
					} }
					checked={ attributes.first_name }
				/>
				<ToggleControl
					label={ __( 'Show Last Name', 'manisha-makhija' ) }
					onChange={ ( value ) => {
						setAttributes( {
							last_name: value,
						} );
					} }
					checked={ attributes.last_name }
				/>
				<ToggleControl
					label={ __( 'Show Email', 'manisha-makhija' ) }
					onChange={ ( value ) => {
						setAttributes( {
							email: value,
						} );
					} }
					checked={ attributes.email }
				/>
				<ToggleControl
					label={ __( 'Show Date', 'manisha-makhija' ) }
					onChange={ ( value ) => {
						setAttributes( {
							date: value,
						} );
					} }
					checked={ attributes.date }
				/>
			</PanelBody>
		</InspectorControls>,
	];

	useEffect( () => {
		apiFetch( {
			path: mmApiDataDisplayBlock.custom_endpoint,
		} ).then(
			( result ) => {
				setIsLoaded( true );
				setRecord( result );
			},
			( err ) => {
				setIsLoaded( true );
				setError( err );
			}
		);
	}, [] );

	if ( error ) {
		return (
			<p>
				{ sprintf(
					/* Translators: %s - error message. */
					__( 'ERROR: %s', 'manisha-makhija' ),
					error.message
				) }{ ' ' }
			</p>
		);
	} else if ( ! isLoaded ) {
		return <p>{ __( 'Loading response…', 'manisha-makhija' ) } </p>;
	} else if ( record ) {
		const dataHeaders = record.data.headers;
		const dataRows = record.data.rows;
		if ( dataHeaders.length < 0 || Object.keys( dataRows ).length < 0 ) {
			return <p>{ __( 'No data found in API', 'manisha-makhija' ) }</p>;
		}
		htmlStructure.push( [
			<div
				key="manisha-makhija-table-data-wrap"
				className="manisha-makhija-table-data-wrap"
			>
				<table
					border="1"
					style={ { width: '100%', textAlign: 'center' } }
				>
					{ record.title && (
						<tr>
							<td
								colSpan="5"
								style={ {
									fontSize: 'xx-large',
									padding: '25px',
								} }
							>
								{ record.title }
							</td>
						</tr>
					) }
					<tr>
						<TableHeader
							header={ dataHeaders }
							attributes={ attributes }
						/>
					</tr>
					<TableRows rows={ dataRows } attributes={ attributes } />
				</table>
			</div>,
		] );
	}

	return htmlStructure;
}

function TableHeader( { header, attributes } ) {
	const headerStructure = new Array();
	for ( let i = 0; i < header.length; i++ ) {
		const sanitizeHeaderValue = header[ i ].replace( ' ', '_' );
		const headerKey = sanitizeHeaderValue.toLowerCase();
		headerStructure.push( [
			<>{ attributes[ headerKey ] && <th>{ header[ i ] }</th> }</>,
		] );
	}
	return headerStructure;
}

function TableRows( { rows, attributes } ) {
	const recordRows = new Array();
	Object.values( rows ).forEach( ( val ) => {
		const date = new Date( val.date * 1000 );
		recordRows.push( [
			<tr key="mm-api-data-display-table-rows">
				{ attributes.id && <td>{ val.id }</td> }
				{ attributes.first_name && <td>{ val.fname }</td> }
				{ attributes.last_name && <td>{ val.lname }</td> }
				{ attributes.email && <td>{ val.email }</td> }
				{ attributes.date && (
					<td>{ date.toLocaleDateString( 'en-US' ) }</td>
				) }
			</tr>,
		] );
	} );
	return recordRows;
}
