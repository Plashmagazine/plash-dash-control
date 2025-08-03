import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { AuthProvider } from "@/components/auth/AuthProvider";
import { ProtectedRoute } from "@/components/auth/ProtectedRoute";

// Pages
import { Login } from "./pages/Login";
import { Unauthorized } from "./pages/Unauthorized";
import { AdminDashboard } from "./pages/admin/AdminDashboard";
import { AdminDiagnostics } from "./pages/admin/AdminDiagnostics";
import { CollaboratorDashboard } from "./pages/collaborator/CollaboratorDashboard";
import NotFound from "./pages/NotFound";

const queryClient = new QueryClient();

const App = () => (
  <QueryClientProvider client={queryClient}>
    <TooltipProvider>
      <AuthProvider>
        <Toaster />
        <Sonner />
        <BrowserRouter>
          <Routes>
            {/* Public Routes */}
            <Route path="/login" element={<Login />} />
            <Route path="/unauthorized" element={<Unauthorized />} />
            
            {/* Redirect root to login */}
            <Route path="/" element={<Navigate to="/login" replace />} />
            
            {/* Admin Routes */}
            <Route path="/admin" element={
              <ProtectedRoute allowedRoles={['admin']}>
                <AdminDashboard />
              </ProtectedRoute>
            } />
            <Route path="/admin/diagnostics" element={
              <ProtectedRoute allowedRoles={['admin']}>
                <AdminDiagnostics />
              </ProtectedRoute>
            } />
            
            {/* Collaborator Routes */}
            <Route path="/collaborator" element={
              <ProtectedRoute allowedRoles={['collaborator']}>
                <CollaboratorDashboard />
              </ProtectedRoute>
            } />
            
            {/* Athlete Routes */}
            <Route path="/athlete" element={
              <ProtectedRoute allowedRoles={['athlete']}>
                <div>Athlete Dashboard (Em desenvolvimento)</div>
              </ProtectedRoute>
            } />
            
            {/* Partner Routes */}
            <Route path="/partner" element={
              <ProtectedRoute allowedRoles={['partner']}>
                <div>Partner Dashboard (Em desenvolvimento)</div>
              </ProtectedRoute>
            } />
            
            {/* 404 */}
            <Route path="*" element={<NotFound />} />
          </Routes>
        </BrowserRouter>
      </AuthProvider>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
