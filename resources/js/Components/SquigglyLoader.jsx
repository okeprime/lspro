import React from 'react';

export default function SquigglyLoader({ color = '#16a34a', width = 36, height = 36 }) {
    return (
        <div style={{ position: 'relative', width: width, height: height, display: 'flex', justifyContent: 'center', alignItems: 'center' }}>
            <img src="/assets/kementan.png" alt="Loading" style={{ width: '55%', height: 'auto', position: 'absolute', zIndex: 1 }} />
            <svg 
                width="100%" 
                height="100%" 
                viewBox="0 0 100 100" 
                style={{ position: 'absolute', zIndex: 2, animation: 'spin-btn-loader 1s linear infinite' }}
            >
                <circle cx="50" cy="50" r="44" fill="none" stroke="rgba(255,255,255,0.3)" strokeWidth="8" />
                <circle cx="50" cy="50" r="44" fill="none" stroke="#ffffff" strokeWidth="8" strokeLinecap="round" strokeDasharray="80 200" />
            </svg>
            <style>
                {`
                @keyframes spin-btn-loader {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                `}
            </style>
        </div>
    );
}
