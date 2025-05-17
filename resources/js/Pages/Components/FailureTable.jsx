import React, { useEffect, useState } from 'react';
import axios from 'axios';

export default function FailureTable({ onClose }) {
    const [logs, setLogs] = useState([]);

    useEffect(() => {
        axios.get('/admin/failures')
            .then(res => setLogs(res.data))
            .catch(err => console.error("Failed to fetch failures", err));
    }, []);

    return (
        <div className="mt-6 bg-red-50 border border-red-200 p-4 rounded shadow">
            <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg font-bold text-red-600">❌ Recent Failures</h3>
                <button
                    onClick={onClose}
                    className="text-sm text-red-500 hover:underline"
                >
                    Close
                </button>
            </div>
            <div className="overflow-auto max-h-96">
                <table className="min-w-full text-sm table-auto">
                    <thead className="bg-red-100">
                        <tr>
                            <th className="px-3 py-1 text-left">Endpoint</th>
                            <th className="px-3 py-1 text-left">Action</th>
                            <th className="px-3 py-1 text-left">IP</th>
                            <th className="px-3 py-1 text-left">Status</th>
                            <th className="px-3 py-1 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        {logs.map((log, i) => (
                            <tr key={i} className="border-t border-red-100">
                                <td className="px-3 py-1">{log.endpoint}</td>
                                <td className="px-3 py-1">{log.action}</td>
                                <td className="px-3 py-1">{log.ip}</td>
                                <td className="px-3 py-1">{log.status}</td>
                                <td className="px-3 py-1">{new Date(log.created_at).toLocaleString()}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
