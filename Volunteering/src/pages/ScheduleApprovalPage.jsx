import PropTypes from 'prop-types';

function ScheduleApprovalPage({ taskRequests }) {

    const handleApprove = (requestId) => {
        console.log('Task request approved:', requestId);
 
    };

    const handleDeny = (requestId) => {
        console.log('Task request denied:', requestId);
     
    };

    return (
        <div className="flex justify-center items-center h-screen">
            <div className="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <h2 className="text-lg font-semibold mb-4">Task Requests Approval</h2>
                <div className="overflow-auto max-h-96">
                    {taskRequests.map(request => (
                        <div key={request.id} className="flex items-center mb-4">
                            <div>
                                <p className="text-sm mb-1"><strong>Title:</strong> {request.title}</p>
                                <p className="text-sm mb-1"><strong>Description:</strong> {request.description}</p>
                                <p className="text-sm mb-1"><strong>Deadline:</strong> {request.deadline}</p>
                                <p className="text-sm mb-1"><strong>Requested By:</strong> {request.requester}</p>
                            </div>
                            <div className="ml-auto">
                                <button
                                    className="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline mr-2"
                                    onClick={() => handleApprove(request.id)}
                                >
                                    Approve
                                </button>
                                <button
                                    className="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline"
                                    onClick={() => handleDeny(request.id)}
                                >
                                    Deny
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}

export default ScheduleApprovalPage;
ScheduleApprovalPage.propTypes = {
    taskRequests: PropTypes.array.isRequired
};