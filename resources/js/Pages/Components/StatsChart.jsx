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

export default function StatsChart() {
    const [data, setData] = useState([]);

    useEffect(() => {
        axios.get('/admin/stats')
            .then(res => {
                const byDay = res.data.by_day || {};
                const chartData = Object.entries(byDay).map(([date, count]) => ({
                    date,
                    count: parseInt(count),
                }));
                setData(chartData);
            })
            .catch(err => {
                console.error("Failed to load stats:", err);
            });
    }, []);

    return (
        <div className="bg-white p-6 rounded shadow mt-8">
            <h3 className="text-lg font-bold mb-4">📊 API Calls per Day</h3>
            <ResponsiveContainer width="100%" height={300}>
                <BarChart data={data}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="date" />
                    <YAxis />
                    <Tooltip />
                    <Legend />
                    <Bar dataKey="count" fill="#3b82f6" />
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}
