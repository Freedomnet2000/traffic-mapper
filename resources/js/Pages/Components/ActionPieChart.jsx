import React, { useEffect, useState } from 'react';
import axios from 'axios';
import {
    PieChart,
    Pie,
    Cell,
    Tooltip,
    Legend,
    ResponsiveContainer
} from 'recharts';

const COLORS = ['#3b82f6', '#10b981', '#f59e0b']; // כחול, ירוק, כתום

export default function ActionPieChart() {
    const [data, setData] = useState([]);

    useEffect(() => {
        axios.get('/admin/stats')
            .then(res => {
                const byAction = res.data.by_action || {};
                const formatted = Object.entries(byAction).map(([key, value]) => ({
                    name: key,
                    value: parseInt(value),
                }));
                setData(formatted);
            })
            .catch(err => {
                console.error("Failed to load pie chart:", err);
            });
    }, []);

    return (
        <div className="bg-white p-6 rounded shadow mt-8">
            <h3 className="text-lg font-bold mb-4">🧩 Action Distribution</h3>
            <ResponsiveContainer width="100%" height={300}>
                <PieChart>
                    <Pie
                        data={data}
                        dataKey="value"
                        nameKey="name"
                        cx="50%"
                        cy="50%"
                        outerRadius={100}
                        fill="#8884d8"
                        label
                    >
                        {data.map((entry, index) => (
                            <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                        ))}
                    </Pie>
                    <Tooltip />
                    <Legend />
                </PieChart>
            </ResponsiveContainer>
        </div>
    );
}
