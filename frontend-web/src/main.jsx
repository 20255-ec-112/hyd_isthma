import React, { Suspense } from 'react';
import ReactDOM from 'react-dom/client';

// Perfect Scrollbar
import 'react-perfect-scrollbar/dist/css/styles.css';

// Tailwind css
import './index.css';

// Router
import { RouterProvider } from 'react-router-dom';
import router from './router/index';
// redux
import { Provider } from 'react-redux';
import store from './store/index';

ReactDOM.createRoot(document.getElementById('root')).render(
    <React.StrictMode>
        <Suspense>
            <Provider store={store}>
                <RouterProvider router={router} />
            </Provider>
        </Suspense>
    </React.StrictMode>
);
