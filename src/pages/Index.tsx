import React, { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '@/hooks/useAuth';
import { Card, CardContent } from '@/components/ui/card';
import { Loader2 } from 'lucide-react';

const Index = () => {
  const { isAuthenticated, user, loading } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    if (!loading) {
      if (isAuthenticated && user) {
        // Redirecionar baseado no tipo de usuário
        switch (user.role) {
          case 'admin':
            navigate('/admin');
            break;
          case 'collaborator':
            navigate('/collaborator');
            break;
          case 'athlete':
            navigate('/athlete');
            break;
          case 'partner':
            navigate('/partner');
            break;
          default:
            navigate('/login');
        }
      } else {
        navigate('/login');
      }
    }
  }, [isAuthenticated, user, loading, navigate]);

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-editorial">
      <Card className="w-96">
        <CardContent className="p-8 text-center">
          <Loader2 className="w-8 h-8 animate-spin mx-auto mb-4 text-primary" />
          <h2 className="text-xl font-semibold mb-2">Plash Magazine</h2>
          <p className="text-muted-foreground">Redirecionando...</p>
        </CardContent>
      </Card>
    </div>
  );
};

export default Index;
