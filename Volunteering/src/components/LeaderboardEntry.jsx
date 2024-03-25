import PropTypes from 'prop-types'

export default function LeaderboardEntry({ position, name, stats }) {

    return (
        <li>
            {position}<br/>
            {name}<br/>
            {stats}
        </li>
    );
}

LeaderboardEntry.propTypes = {
    position: PropTypes.number.isRequired,
    name: PropTypes.string.isRequired,
    stats: PropTypes.number.isRequired
}
