/**
 * External dependencies
 */
const { isUndefined, pickBy } = lodash;
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
const {
	Component,
	Fragment,
} = wp.element;

const { __ } = wp.i18n;
const { compose } = wp.compose;
const { withSelect } = wp.data;

const {
	InspectorControls,
} = wp.editor;

const {
	PanelBody,
	Placeholder,
	RangeControl,
	ServerSideRender,
	Spinner,
	TextControl,
} = wp.components;

/**
 * Internal dependencies
 */
import CategorySelect from '../../components/category-select';
import AuthorSelect from '../../components/author-select';
import OrderSelect from '../../components/order-select';
import MagazinePost from '../../components/magazine-post';

/**
 * Block Edit Component
 */
class MagazineGridEdit extends Component {
	render() {
		const {
			attributes,
			className,
			setAttributes,
			latestPosts,
		} = this.props;

		const {
			categories,
			tags,
			author,
			order,
			orderBy,
			numberOfPosts,
			offset,
		} = attributes;

		const blockClasses = classnames( className, 'tz-magazine-block' );

		const inspectorControls = (
			<InspectorControls>

				<PanelBody title={ __( 'Content Settings', 'themezee-blocks' ) }>

					<CategorySelect
						selectedCategoryId={ categories }
						onCategoryChange={ ( value ) => setAttributes( { categories: '' !== value ? value : undefined } ) }
					/>

					<TextControl
						label={ __( 'Tags' ) }
						value={ tags }
						onChange={ ( value ) => setAttributes( { tags: '' !== value ? value : undefined } ) }
					/>

					<AuthorSelect
						selectedAuthorId={ author }
						onAuthorChange={ ( value ) => setAttributes( { author: '' !== value ? value : undefined } ) }
					/>

					<OrderSelect
						{ ...{ order, orderBy } }
						onOrderChange={ ( value ) => setAttributes( { order: value } ) }
						onOrderByChange={ ( value ) => setAttributes( { orderBy: value } ) }
					/>

					<RangeControl
						key="tz-number-of-posts-control"
						label={ __( 'Number of posts', 'themezee-blocks' ) }
						value={ numberOfPosts }
						onChange={ ( value ) => setAttributes( { numberOfPosts: value } ) }
						min={ 1 }
						max={ 30 }
					/>

					<RangeControl
						key="tz-offset-control"
						label={ __( 'Offset', 'themezee-blocks' ) }
						value={ offset }
						onChange={ ( value ) => setAttributes( { offset: value } ) }
						min={ 0 }
						max={ 30 }
					/>

				</PanelBody>

			</InspectorControls>
		);

		const hasPosts = Array.isArray( latestPosts ) && latestPosts.length;
		if ( ! hasPosts ) {
			return (
				<Fragment>

					{ inspectorControls }

					<Placeholder
						icon="format-aside"
						label={ __( 'Magazine Grid', 'themezee-blocks' ) }
					>
						{ ! Array.isArray( latestPosts ) ?
							<Spinner /> :
							__( 'No posts found.', 'themezee-blocks' )
						}
					</Placeholder>

				</Fragment>
			);
		}

		// Removing posts from display should be instant.
		const displayPosts = latestPosts.length > numberOfPosts ?
			latestPosts.slice( 0, numberOfPosts ) :
			latestPosts;

		return (
			<Fragment>

				{ inspectorControls }

				<div className={ blockClasses }>
					<div className="tz-magazine-columns tz-magazine-columns-3">

						{ displayPosts.map( ( post, i ) => {
							return (
								<MagazinePost key={ i } post={ post } />
							);
						} ) }

					</div>
				</div>

			</Fragment>
		);
	}
}

export default compose( [
	withSelect( ( select, props ) => {
		const { categories, tags, author, numberOfPosts, order, orderBy, offset } = props.attributes;
		const { getEntityRecords } = select( 'core' );

		// Retrieve Tag IDs from Tag names.
		let tagsIDs;
		if ( ! ( ! tags || 0 === tags.length ) ) {
			const tagsObj = getEntityRecords( 'taxonomy', 'post_tag', { per_page: -1, slug: tags } );
			if ( tagsObj ) {
				tagsIDs = Object.keys( tagsObj ).reduce( ( str, key ) => str + tagsObj[ key ].id + ',', '' );
				tagsIDs = tagsIDs.slice( 0, -1 );
			}
		}

		// Query Posts.
		const latestPostsQuery = pickBy( {
			categories,
			tags: '' !== tagsIDs ? tagsIDs : '99999999999999999999999999999999999', // dirty hack
			author,
			order,
			orderby: orderBy,
			per_page: numberOfPosts,
			offset,
		}, ( value ) => ! isUndefined( value ) );

		return {
			latestPosts: getEntityRecords( 'postType', 'post', latestPostsQuery ),
		};
	} ),
] )( MagazineGridEdit );
