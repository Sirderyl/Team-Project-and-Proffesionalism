import PropTypes from 'prop-types'

export default function LeaderboardEntry({ position, name, stats }) {

    return (
        <li className="list-none rounded-lg text-gray-700 shadow-md p-3 m-2">
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
