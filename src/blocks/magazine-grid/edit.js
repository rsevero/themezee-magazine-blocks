/**
 * External dependencies
 */
const { isUndefined, pickBy } = lodash;

/**
 * WordPress dependencies
 */
const {
	Component,
	Fragment,
	RawHTML,
} = wp.element;

const { __ } = wp.i18n;
const { compose } = wp.compose;
const { addQueryArgs } = wp.url;
const { withSelect } = wp.data;

const {
	InspectorControls,
} = wp.editor;

const {
	PanelBody,
	Placeholder,
	Spinner,
	QueryControls,
} = wp.components;

/**
 * Module Constants
 */
const CATEGORIES_LIST_QUERY = {
	per_page: -1,
};

/**
 * Block Edit Component
 */
class MagazineGridEdit extends Component {
	constructor() {
		super( ...arguments );
		this.state = {
			categoriesList: [],
		};
	}

	componentDidMount() {
		this.isStillMounted = true;
		this.fetchRequest = wp.apiFetch( {
			path: addQueryArgs( '/wp/v2/categories', CATEGORIES_LIST_QUERY ),
		} ).then(
			( categoriesList ) => {
				if ( this.isStillMounted ) {
					this.setState( { categoriesList } );
				}
			}
		).catch(
			() => {
				if ( this.isStillMounted ) {
					this.setState( { categoriesList: [] } );
				}
			}
		);
	}

	componentWillUnmount() {
		this.isStillMounted = false;
	}

	render() {
		const { categoriesList } = this.state;

		const {
			attributes,
			setAttributes,
			latestPosts,
		} = this.props;

		const {
			order,
			orderBy,
			categories,
			postsToShow,
		} = attributes;

		const inspectorControls = (
			<InspectorControls>

				<PanelBody title={ __( 'Content Settings', 'themezee-blocks' ) }>

					<QueryControls
						{ ...{ order, orderBy } }
						numberOfItems={ postsToShow }
						categoriesList={ categoriesList }
						selectedCategoryId={ categories }
						onOrderChange={ ( value ) => setAttributes( { order: value } ) }
						onOrderByChange={ ( value ) => setAttributes( { orderBy: value } ) }
						onCategoryChange={ ( value ) => setAttributes( { categories: '' !== value ? value : undefined } ) }
						onNumberOfItemsChange={ ( value ) => setAttributes( { postsToShow: value } ) }
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
						icon="admin-post"
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
		const displayPosts = latestPosts.length > postsToShow ? latestPosts.slice( 0, postsToShow ) : latestPosts;

		return (
			<Fragment>

				{ inspectorControls }

				<ul className={ this.props.className }>

					{ displayPosts.map( ( post, i ) => {
						const titleTrimmed = post.title.rendered.trim();
						return (
							<li key={ i }>
								<a href={ post.link } target="_blank" rel="noreferrer noopener">
									{ titleTrimmed ? (
										<RawHTML>
											{ titleTrimmed }
										</RawHTML>
									) :
										__( '(Untitled)', 'themezee-blocks' )
									}
								</a>
							</li>
						);
					} ) }

				</ul>

			</Fragment>
		);
	}
}

export default compose( [
	withSelect( ( select, props ) => {
		const { postsToShow, order, orderBy, categories } = props.attributes;
		const { getEntityRecords } = select( 'core' );
		const latestPostsQuery = pickBy( {
			categories,
			order,
			orderby: orderBy,
			per_page: postsToShow,
		}, ( value ) => ! isUndefined( value ) );

		return {
			latestPosts: getEntityRecords( 'postType', 'post', latestPostsQuery ),
		};
	} ),
] )( MagazineGridEdit );