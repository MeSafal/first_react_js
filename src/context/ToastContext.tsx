import React, { createContext, useContext, useState, useCallback } from 'react';
import { CheckCircle, AlertCircle, Info, X } from 'lucide-react';

interface Toast {
    id: string;
    message: string;
    type: 'success' | 'error' | 'info';
}

interface ToastContextType {
    showToast: (message: string, type: 'success' | 'error' | 'info') => void;
}

const ToastContext = createContext<ToastContextType | undefined>(undefined);

export const useToast = () => {
    const context = useContext(ToastContext);
    if (!context) {
        throw new Error('useToast must be used within ToastProvider');
    }
    return context;
};

export const ToastProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const [toasts, setToasts] = useState<Toast[]>([]);

    const showToast = useCallback((message: string, type: 'success' | 'error' | 'info' = 'success') => {
        const id = Math.random().toString(36).substr(2, 9);
        setToasts(prev => [...prev, { id, message, type }]);

        setTimeout(() => {
            setToasts(prev => prev.filter(t => t.id !== id));
        }, 3000);
    }, []);

    const removeToast = (id: string) => {
        setToasts(prev => prev.filter(t => t.id !== id));
    };

    const getIcon = (type: string) => {
        switch (type) {
            case 'success': return <CheckCircle size={20} className="text-success" />;
            case 'error': return <AlertCircle size={20} className="text-danger" />;
            case 'info': return <Info size={20} className="text-primary" />;
        }
    };

    return (
        <ToastContext.Provider value={{ showToast }}>
            {children}
            <div className="position-fixed bottom-0 end-0 p-3" style={{ zIndex: 9999 }}>
                {toasts.map(toast => (
                    <div
                        key={toast.id}
                        className="toast show mb-2 border-0 shadow-lg"
                        role="alert"
                        style={{ minWidth: '300px' }}
                    >
                        <div className="toast-body d-flex align-items-center gap-3 p-3">
                            {getIcon(toast.type)}
                            <span className="flex-grow-1 fw-medium">{toast.message}</span>
                            <button
                                onClick={() => removeToast(toast.id)}
                                className="btn btn-sm btn-link text-muted p-0"
                            >
                                <X size={16} />
                            </button>
                        </div>
                    </div>
                ))}
            </div>
        </ToastContext.Provider>
    );
};
