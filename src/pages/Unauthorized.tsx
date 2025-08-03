import React from 'react';
import { Link } from 'react-router-dom';
import { Button } from '@/components/ui/button';
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from '@/components/ui/card';
import { Shield, ArrowLeft } from 'lucide-react';

export const Unauthorized: React.FC = () => {
  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-editorial p-4">
      <Card className="w-full max-w-md text-center shadow-editorial">
        <CardHeader>
          <div className="mx-auto w-16 h-16 bg-destructive rounded-full flex items-center justify-center mb-4">
            <Shield className="w-8 h-8 text-destructive-foreground" />
          </div>
          <CardTitle className="text-2xl font-bold text-destructive">
            Acesso Negado
          </CardTitle>
          <CardDescription>
            Você não tem permissão para acessar esta área da plataforma.
          </CardDescription>
        </CardHeader>
        
        <CardContent className="space-y-4">
          <p className="text-sm text-muted-foreground">
            Se você acredita que isso é um erro, entre em contato com o administrador.
          </p>
          
          <div className="space-y-2">
            <Button asChild variant="outline" className="w-full">
              <Link to="/">
                <ArrowLeft className="w-4 h-4 mr-2" />
                Voltar ao Login
              </Link>
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  );
};