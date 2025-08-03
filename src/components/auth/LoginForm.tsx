import React, { useState } from 'react';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Eye, EyeOff, LogIn } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

interface LoginFormProps {
  userType?: 'admin' | 'collaborator' | 'athlete' | 'partner';
}

export const LoginForm: React.FC<LoginFormProps> = ({ userType = 'admin' }) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  
  const { login } = useAuth();
  const navigate = useNavigate();

  const userTypeConfig = {
    admin: {
      title: 'Painel Administrativo',
      description: 'Acesso para administradores da Plash Magazine',
      redirectPath: '/admin',
      demoEmail: 'admin@plashmagazine.com',
      demoPassword: 'admin123'
    },
    collaborator: {
      title: 'Painel do Colaborador',
      description: 'Acesso para colaboradores e fotógrafos',
      redirectPath: '/collaborator',
      demoEmail: 'colaborador@plash.com',
      demoPassword: 'demo123'
    },
    athlete: {
      title: 'Painel do Atleta',
      description: 'Acesso para skatistas e participantes',
      redirectPath: '/athlete',
      demoEmail: 'atleta@plash.com',
      demoPassword: 'demo123'
    },
    partner: {
      title: 'Painel da Editora Parceira',
      description: 'Acesso para editoras parceiras',
      redirectPath: '/partner',
      demoEmail: 'editora@plash.com',
      demoPassword: 'demo123'
    }
  };

  const config = userTypeConfig[userType];

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const result = await login(email, password);
      
      if (result.success) {
        navigate(config.redirectPath);
      } else {
        setError(result.error || 'Erro ao fazer login');
      }
    } catch (err) {
      setError('Erro interno do sistema');
    } finally {
      setLoading(false);
    }
  };

  const fillDemoCredentials = () => {
    setEmail(config.demoEmail);
    setPassword(config.demoPassword);
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-editorial p-4">
      <Card className="w-full max-w-md shadow-editorial">
        <CardHeader className="text-center">
          <div className="mx-auto w-16 h-16 bg-primary rounded-full flex items-center justify-center mb-4">
            <LogIn className="w-8 h-8 text-primary-foreground" />
          </div>
          <CardTitle className="text-2xl font-bold">{config.title}</CardTitle>
          <CardDescription>{config.description}</CardDescription>
        </CardHeader>
        
        <form onSubmit={handleSubmit}>
          <CardContent className="space-y-4">
            {error && (
              <Alert variant="destructive">
                <AlertDescription>{error}</AlertDescription>
              </Alert>
            )}
            
            <div className="space-y-2">
              <Label htmlFor="email">Email</Label>
              <Input
                id="email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="seu@email.com"
                required
                disabled={loading}
              />
            </div>
            
            <div className="space-y-2">
              <Label htmlFor="password">Senha</Label>
              <div className="relative">
                <Input
                  id="password"
                  type={showPassword ? 'text' : 'password'}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="Sua senha"
                  required
                  disabled={loading}
                />
                <Button
                  type="button"
                  variant="ghost"
                  size="icon-sm"
                  className="absolute right-2 top-1/2 -translate-y-1/2"
                  onClick={() => setShowPassword(!showPassword)}
                  disabled={loading}
                >
                  {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </Button>
              </div>
            </div>
            
            {/* Demo credentials helper */}
            <div className="p-3 bg-muted rounded-md">
              <p className="text-sm text-muted-foreground mb-2">Credenciais de demonstração:</p>
              <Button
                type="button"
                variant="outline"
                size="sm"
                onClick={fillDemoCredentials}
                disabled={loading}
                className="w-full"
              >
                Usar credenciais demo
              </Button>
            </div>
          </CardContent>
          
          <CardFooter>
            <Button
              type="submit"
              variant="editorial"
              size="lg"
              className="w-full"
              disabled={loading}
            >
              {loading ? (
                <>
                  <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-primary-foreground mr-2"></div>
                  Entrando...
                </>
              ) : (
                <>
                  <LogIn className="w-4 h-4 mr-2" />
                  Entrar
                </>
              )}
            </Button>
          </CardFooter>
        </form>
      </Card>
    </div>
  );
};