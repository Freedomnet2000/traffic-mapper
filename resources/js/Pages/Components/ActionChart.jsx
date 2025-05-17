import React, { useEffect, useState } from 'react';
import axios from 'axios';
import {
    ResponsiveContainer,
    BarChart,
    Bar,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    Legend,
} from 'recharts';

export default function ActionChart() {
    const [data, setData] = useState([]);

    useEffect(() => {
        axios.get('/admin/stats')
            .then(res => {
                setData(res.data.by_day_action || []);
            })
            .catch(err => {
                console.error("Failed to load action chart:", err);
            });
    }, []);

    return (
        <div className="bg-white p-6 rounded shadow mt-8">
            <h3 className="text-lg font-bold mb-4">🔁 Calls per Action (Last 7 Days)</h3>
            <ResponsiveContainer width="100%" height={300}>
                <BarChart data={data}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="date" />
                    <YAxis />
                    <Tooltip />
                    <Legend />
                    <Bar dataKey="redirect" stackId="a" fill="#3b82f6" />
                    <Bar dataKey="retrieve" stackId="a" fill="#10b981" />
                    <Bar dataKey="refresh" stackId="a" fill="#f59e0b" />
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}
