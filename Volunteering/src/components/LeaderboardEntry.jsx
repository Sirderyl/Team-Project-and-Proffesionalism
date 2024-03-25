import PropTypes from 'prop-types'

export default function LeaderboardEntry({ position, name, stats }) {

    return (
        <li>
            <span>{position} </span>
            <span>{name} </span>
            <span>{stats} </span>
        </li>
    );
}

LeaderboardEntry.propTypes = {
    position: PropTypes.number.isRequired,
    name: PropTypes.string.isRequired,
    stats: PropTypes.number.isRequired
}
