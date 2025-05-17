import React, { useEffect, useState } from 'react';
import axios from 'axios';

export default function FailureModal({ onClose }) {
    const [logs, setLogs] = useState([]);

    useEffect(() => {
        axios.get('/admin/failures')
            .then(res => setLogs(res.data))
            .catch(err => console.error("Failed to fetch failures", err));
    }, []);

    return (
        <div className="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
            <div className="bg-white w-full max-w-6xl max-h-[90vh] overflow-auto rounded-xl shadow-lg p-6 relative animate-fade-in">
                <button
                    onClick={onClose}
                    className="absolute top-3 right-4 text-gray-500 hover:text-red-500 text-xl font-bold"
                >
                    ×
                </button>
                <h3 className="text-2xl font-bold mb-4 text-red-600">❌ Recent Failed Requests</h3>

                <table className="w-full text-sm table-auto border border-gray-200 rounded overflow-hidden">
                    <thead className="bg-red-100 text-gray-700">
                        <tr>
                            <th className="p-2 text-left">Endpoint</th>
                            <th className="p-2 text-left">Action</th>
                            <th className="p-2 text-left">IP</th>
                            <th className="p-2 text-left">Status</th>
                            <th className="p-2 text-left">Params</th>
                            <th className="p-2 text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        {logs.map((log, i) => (
                            <tr key={i} className="border-t border-gray-100">
                                <td className="p-2">{log.endpoint}</td>
                                <td className="p-2">{log.action}</td>
                                <td className="p-2">{log.ip}</td>
                                <td className="p-2">{log.status}</td>
                                <td className="p-2 font-mono text-xs text-gray-700 whitespace-pre-wrap break-all bg-gray-50 rounded">
                                    {formatParams(log.params)}
                                </td>
                                <td className="p-2">{new Date(log.created_at).toLocaleString()}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

function formatParams(paramString) {
    try {
        const parsed = JSON.parse(paramString);
        return JSON.stringify(parsed, null, 2);
    } catch (e) {
        return paramString || '-';
    }
}
