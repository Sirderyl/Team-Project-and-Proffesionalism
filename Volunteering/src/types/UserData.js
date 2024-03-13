import PropTypes from 'prop-types';

/**
 * PropTypes for UserData. Optional by default.
 */
export default PropTypes.shape({
    userId: PropTypes.number.isRequired,
    token: PropTypes.string.isRequired,
});

/**
 * @typedef {Object} UserData
 * @property {number} userId
 * @property {string} name
 */
