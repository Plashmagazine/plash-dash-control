import React from 'react';
import { AppLayout } from '@/components/layout/AppLayout';
import { SystemDiagnostics } from '@/components/diagnostics/SystemDiagnostics';

export const AdminDiagnostics: React.FC = () => {
  return (
    <AppLayout>
      <SystemDiagnostics />
    </AppLayout>
  );
};