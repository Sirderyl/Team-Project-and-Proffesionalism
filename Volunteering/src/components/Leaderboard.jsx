import PropTypes from 'prop-types'
import { useState } from 'react';
import { v4 } from 'uuid';
import LeaderboardEntry from "./LeaderboardEntry";

export default function Leaderboard() {
    const [entries] = useState([
        { id: v4(), position: 1, name: "Some Person 1", stats: 100 },
        { id: v4(), position: 2, name: "Some Person 2", stats: 50 },
        { id: v4(), position: 3, name: "Some Person 3", stats: 20 }
    ]);

    return (
        entries.map(entry => (
            <LeaderboardEntry
                key={entry.id}
                position={entry.position}
                name={entry.name}
                stats={entry.stats}
            />
        ))
    );
}

Leaderboard.propTypes = {
    userData: PropTypes.object.isRequired,
    tasks: PropTypes.array.isRequired
}
